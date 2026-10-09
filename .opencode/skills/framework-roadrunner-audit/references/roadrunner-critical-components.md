# RoadRunner-critical framework components

Source of truth: `vendor/omega-mvc/framework/src/Omega/`. `rr` = RoadRunner. The framework does **not**
depend on RoadRunner at runtime — `psr/http-message ^2.0` + `psr/http-factory ^1.1` are its only HTTP deps;
RoadRunner and the PSR-7/PSR-17 implementations are supplied by the application.

## Component map

| Area | Component | Role under RoadRunner |
| --- | --- | --- |
| Worker loop | `Http\RoadRunnerWorker`, `Http\RoadRunnerResponderInterface` | Persistent loop; `waitRequest()` (null = idle, false = stop) → handle → `respond()`. Canonical responder: `Spiral\RoadRunner\Http\PSR7Worker`. |
| Request bridge | `Http\RequestFactory` `::fromPsr7ServerRequest()` | Builds the Omega `Request` **without superglobals**, from the PSR-7 object. |
| Response bridge | `Http\ResponseFactory` `::toPsr7()` | Omega `Response` → PSR-7 response (status text, JSON body, headers). |
| Kernel | `Http\Http` | `handle()`, middleware pipeline, `terminate()`, `resetForRequest()`. |
| App reset | `Application\AbstractApplication` `::resetForRequest()` / `::bootRoutes()` | Per-request reset + route re-registration; container lives across requests. |
| Container | `Container\Container` (`setRequestScoped`, `resetRequestScope`, `flush`) | Request-scoped bindings discarded between requests. |
| Routing | `Router\RouteServiceProvider` (static `$scheduleLoaded`, `registerWebRoutes`) | Repopulates the static route table each request; schedule loaded once/process. |
| Config | `Config\Bootstrapper\ConfigBootstrapper` | `bootstrap()` runs each request, resolved config cached. |
| Views | `View\Templator`, `View\Templator\DirectiveTemplator` | Singleton; must be cleared per request. |
| Database | `Database\DatabaseManager::resetConnectionsForRequest`, `Database\AbstractConnection` | Flush logs, roll back dangling transaction, reconnect dead PDO in long-lived worker. |
| Cache | `Cache\Storage\MemoryStorage` | TTL keys written-but-never-read can leak for the process lifetime. |
| Errors | `Exceptions\Bootstrapper\HandleExceptions` (+ the app's Whoops `bootedCallback`) | Error/exception handler state must be balanced per request. |
| Cron | `Cron\Schedule`, `Cron\ScheduleTime` | A worker advancing a fixed schedule over time. |

Existing harness: `tests/Unit/Http/RoadRunnerMultiRequestTest.php` boots one app, handles two `/test`
requests, calls `terminate()` between them, and asserts `Router::getRoutes()` is non-empty (the route-table
regression). `afterEach` calls `$this->app->flush()` + `HandleExceptions::resetHandlersState()`.

## Per-component invariants to check

- **Worker loop** — `waitRequest()` tri-state handled; `respond()` called on success **and** on throw;
  `Http::terminate()` runs **unconditionally** at the end of every served request so `resetForRequest()`
  always executes; the loop exits cleanly on `false` and idles on `null`.
- **Request bridge** — no superglobal read; headers lowercased and comma-joined; `X-HTTP-Method-Override`
  honored **only** for POST and only when it matches `^[A-Z]+$`; remote addr from server params; query,
  parsed body, cookies and files all carried; raw body string.
- **Response bridge** — status code + canonical reason phrase; protocol version preserved; array content
  JSON-encoded; **every** header value preserved (multi-value / `Set-Cookie` must not collapse); body stream.
- **Kernel** — `request` / `Request::class` marked request-scoped; bootstrap idempotent per request; the
  dispatcher holds no cross-request state; exceptions go to `ExceptionHandler`, never escape silently.
- **App reset** — `resetForRequest()` clears terminate/booting/booted callbacks, request-scoped instances,
  static `Router`, facades, Templator dependencies, then re-registers web routes. Determine precisely what
  **must** persist (process-lifetime providers, config, schedule) versus what is reset.
- **Routing** — the static table is rebuilt every request after `Router::reset()`; the route cache is
  unserialized with an `allowed_classes` allow-list and `max_depth`; `require` (not `require_once`) is used
  so `routes/web.php` re-executes; schedule stays loaded once.
- **DB** — connections survive but are validated/reconnected; dangling transactions rolled back; logs flush.
- **Errors** — handler stacks are balanced after each request; Whoops handlers do not stack.

## Candidate gaps (investigation leads — reproduce before changing)

1. `RoadRunnerWorker::run()` — the `finally` calls `terminate()` **only when both `$omegaRequest` and
   `$omegaResponse` are non-null**. If `makeRequest()` or `handle()` throws, the reset is skipped, so state
   can leak into the next request. Also the `catch` returns `new Response($th->getMessage(), 500)`, exposing
   internal exception text to the client. *(highest priority)*
2. `AbstractApplication::resetForRequest()` clears `bootedCallbacks` every request; the app's Whoops handler
   is installed from a `bootedCallback`. Verify whether that callback is meant to re-run and whether the
   handler stacks stay balanced in a long-lived worker.
3. `ResponseFactory::toPsr7()` casts each header value with `(string) $value` + `withHeader()`; a
   multi-value header (e.g. `Set-Cookie` array) may collapse or produce `"Array"`. Verify
   `Response::$headers->toArray()` shape.
4. `RequestFactory::fromPsr7ServerRequest()` flattens query/body/cookies via `normalizeToStringArray()`,
   which keeps only scalar values — nested arrays are silently dropped. Confirm intended.
5. Uploaded files take `tmp_name` from the PSR-7 stream metadata `uri`, which may be empty for in-memory
   RoadRunner uploads. Verify.
6. `RouteServiceProvider::resolveRouteCallable()` unserializes route cache entries with
   `allowed_classes => [UnsignedSerializableClosure, Native]`, `max_depth => 32`; signed entries are
   intentionally rejected. Confirm no other class can be instantiated from a crafted cache.
7. Memory growth across a long-lived worker: `Cache\Storage\MemoryStorage` TTL entries that are written and
   never read, and `Support\ReflectionClosure` static token caches (never reset).
8. `RouteServiceProvider::boot()` has a nested `if` with misleading indentation around the schedule load;
   `self::$scheduleLoaded` is process-static, so schedule/config changes are not picked up mid-process.
9. `HandleExceptions`/`HandleExceptions::resetHandlersState()` — confirm the production request path (not
   just tests) balances error/exception handler state.
