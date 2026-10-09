# Whole-framework component map

Source of truth: `vendor/omega-mvc/framework/src/Omega/`. For every area: role, key classes, the
invariants to verify, and candidate leads. Leads are **hypotheses** — reproduce before treating as bugs.

Areas marked *(extension point)* are subclassed by the starter app (`app/Kernel/*`); regressions there
break the application, so weigh API-contract stability heavily.

---

## Application
- **Classes:** `AbstractApplication`, `Application`, `ApplicationInterface`, `ApplicationManifest`,
  `helper.php`; `Bootstrapper/*`.
- **Role:** boot lifecycle — create the container, run bootstrap passes, register service providers,
  load env/config, and reset per-request state.
- **Invariants:** boot order is deterministic; bootstrap passes are idempotent where required; the
  manifest (version/hash/aliases) matches what providers declare; each provider is registered and
  booted exactly once per boot and re-booted appropriately per request; `resetForRequest()` clears
  every per-request artefact and re-registers routes; process-lifetime state is explicitly identified.
- **Leads:** manifest/provider drift (a provider listed but not booted, or vice-versa); reset that
  misses a static or facade; boot callbacks cleared too aggressively or not at all; helper functions
  that read global state.

## Archive
- **Classes:** `AdapterInterface`, `Bz2Adapter`, `NativePharEngine`, `PharAdapter`, `PharEngineInterface`,
  `ZipAdapter`.
- **Role:** create/open/extract archives.
- **Invariants:** extraction is confined to the target root (no zip-slip via `../` or absolute paths);
  no PHAR deserialization of untrusted metadata; corrupt/truncated archives fail safely; entry names are
  validated; memory is bounded (streaming where expected).
- **Leads:** path traversal on entry name; symlink entries; phar metadata unserialize; unbounded in-memory
  extraction; overwrite of existing files.

## Cache
- **Classes:** `AbstractCache`, `CacheInterface`, `CacheManager`, `CacheServiceProvider`;
  `Exceptions/*`, `Facade/*`, `Storage/*`, `Traits/*`.
- **Role:** key/value cache with drivers and TTL.
- **Invariants:** TTL is absolute vs sliding as documented; keys are namespaced/prefixed to avoid
  collisions; values serialize/deserialize round-trip; increment/decrement is atomic; `remember`/
  `rememberForever` handle failure; expired entries are purged; `MemoryStorage` does not grow without
  bound in a long-lived process.
- **Leads:** key collision across areas; non-atomic increments; stale `--` prefix handling; TTL written
  but never read leaking for the process lifetime; driver config ignored.

## Collection
- **Classes:** `AbstractCollectionImmutable`, `Collection`, `CollectionImmutable`, `CollectionInterface`,
  `helper.php`; `Exceptions/*`.
- **Role:** array wrapper with map/filter/each/reduce and immutable variant.
- **Invariants:** the immutable variant never mutates and its mutating methods return new instances;
  callbacks receive correct `(value, key)`; `offsetExists`/`offsetGet` semantics agree; iteration order
  preserved; `null` handling documented.
- **Leads:** mutating method on the immutable class returning `$this`; `filter`/`map` dropping keys;
  loose `in_array`; offsets allowing non-int keys.

## Config
- **Classes:** `AbstractConfigRepository`, `ConfigBuilder`, `ConfigRepository`, `ConfigRepositoryInterface`,
  `ConfigSource`, `ConfigTrait`, `MergeStrategy`; `Bootstrapper/*`, `Exceptions/*`, `Facade/*`, `Source/*`.
- **Role:** layered configuration with dot-notation access and merge strategies.
- **Invariants:** source precedence is explicit and stable; dot-notation get/set handles nested keys and
  missing paths; merge strategy (replace/append/recursive) matches its name; cached config matches
  uncached; secret values are not echoed by debug/exception output.
- **Leads:** precedence inverted vs docs; `set` on a scalar parent; deep merge flattening numeric keys;
  cache serialization of closures/objects.

## Console
- **Classes:** `AbstractCommand`, `CommandLoader`, `ConsoleApplication`, `ConsoleBranding`, `Style`;
  `Attribute/*`, `Commands/*`, `Exceptions/*`, `Traits/*`, `stubs/*`.
- **Role:** CLI application, command discovery, argument/option parsing, generators.
- **Invariants:** required/optional args and options parse correctly incl. short flags and `--`; command
  discovery matches attribute names and avoids collisions; exit codes are meaningful; errors go to stderr;
  non-TTY/piped input does not hang interactive prompts; generator stubs write only inside the intended
  directories; `stubs/*` are treated as data, never executed.
- **Leads:** option parsing ambiguity; missing argument not reported; writing a generated file outside
  the project (absolute/`..` names); prompt blocking in CI; ANSI codes emitted to non-TTY.

## Container
- **Classes:** `AbstractServiceProvider`, `AppServiceProviderTrait`, `Container`, `ContainerInterface`,
  `Injector`, `Invoker`, `ReflectionCache`, `Resolver`; `Attribute/*`, `Exceptions/*`.
- **Role:** DI container, autowiring, service providers, request scope.
- **Invariants:** singleton vs transient lifetimes honored; autowiring resolves interfaces/aliases/contextual
  bindings; circular dependencies are detected with a clear error (not infinite recursion); request-scoped
  bindings are discarded between requests while process-lifetime ones persist; reflection cache invalidates
  correctly; invoking an arbitrary callable injects correct params.
- **Leads:** reflection cache key collision / stale after code change; alias loop; request-scoped instance
  captured by a singleton; scalar parameter without default silently null.

## Cron
- **Classes:** `CronServiceProvider`, `InterpolateInterface`, `Log`, `Schedule`, `ScheduleTime`;
  `Facade/*`.
- **Role:** scheduled tasks and cron-expression evaluation.
- **Invariants:** cron expressions (incl. lists/ranges/steps) evaluate correctly across boundaries;
  timezone handling explicit; overlapping runs prevented or documented; one task's failure does not abort
  the rest; schedule is loaded once per process as intended.
- **Leads:** day-of-week 0/7 handling; DST boundaries; no lock between overlapping workers; exception
  from one entry skipping remaining entries.

## Csrf
- **Classes:** `Csrf`, `CsrfInterface`, `CsrfServiceProvider`; `Exceptions/*`, `Facade/*`; plus
  `Middleware\CsrfMiddleware`.
- **Role:** CSRF token issue/verify.
- **Invariants:** tokens are CSPRNG-generated and long enough; comparison is constant-time; token is bound
  to the session and rotated appropriately; safe methods are exempt; token accepted from the documented
  field/header only; missing session fails closed.
- **Leads:** `==`/`strcmp` comparison; predictable token; token not rotated after login; exempting more
  methods than documented.

## Database
- **Classes:** `AbstractConnection`, `ConnectionFactory`, `ConnectionInterface`, `DatabaseManager`,
  `DatabaseServiceProvider`, `LoggerInterface`, `MariadbConnection`, `MysqlConnection`, `PgsqlConnection`,
  `SqliteConnection`, `TransactionInterface`; `Exceptions/*`, `Facades/*`, `Model/*`, `Query/*`,
  `Schema/*`, `Seeder/*`.
- **Role:** connections, query builder, schema builder, model, seeding.
- **Invariants:** identifiers and values are escaped/bound per driver (no injection via identifiers,
  `LIKE`, `IN`); transactions nest correctly and roll back on exception; connections survive and reconnect
  when dead (long-lived worker); schema builder emits correct DDL per driver; model hydration and
  attributes behave; `resetConnectionsForRequest` rolls back dangling transactions and flushes logs.
- **Leads:** unquoted identifier interpolation; `LIKE` wildcard not escaped; `limit/offset` injection;
  nested transaction savepoint bugs; reconnect path not resetting transaction state; driver-specific
  DDL divergence.

## DocBlockGenerator
- **Classes:** `ConstPool`, `Constant`, `Generate`, `Method`, `MethodPool`, `Property`, `PropertyPool`,
  `VarExport`; `Parser/*`, `Providers/*`, `Traits/*`, `VarExport/Compiler/*`, `VarExport/Value/*`.
- **Role:** generate PHP code / docblocks from reflection.
- **Invariants:** generated code is syntactically valid and semantically faithful; string/array/object
  values are exported with correct escaping (quotes, newlines, binary); enums/closures/constants handled
  or explicitly refused; reflection edge cases (variadics, by-ref, union/intersection types, promoted
  props) preserved.
- **Leads:** `var_export`-style escaping of special strings; object export unserializing; type union
  flattened; writing generated file with wrong newline/permissions.

## Environment
- **Classes:** `Env`, `helper.php`.
- **Role:** `.env` loading and environment access.
- **Invariants:** lookup precedence (`$_ENV`/`$_SERVER`/`getenv`/loaded) is explicit; `.env` parsing
  handles quotes, escapes, `#` comments and multiline values; existing vars are not overwritten unless
  configured; type coercion (`true`/`false`/`null`/numbers) matches docs.
- **Leads:** comment char inside quoted value; escaped quotes; precedence differs between CLI/HTTP;
  mutation of `putenv` leaking to child processes.

## Event
- **Classes:** `AbstractEvent`, `Event`, `EventImmutable`, `EventInterface`, `EventServiceProvider`,
  `LazyServiceEventListener`, `ListenersPriorityQueue`, `Priority`, `SubscriberInterface`; `Dispatcher/*`,
  `Events/*`, `Exceptions/*`, `Listeners/*`.
- **Role:** event dispatch with prioritized listeners and subscribers.
- **Invariants:** listener order respects priority and stays stable for ties; stopping propagation works;
  wildcard listeners match as documented; a throwing listener is isolated or propagates as documented;
  lazy listeners resolve from the container exactly once; listeners are not duplicated on re-registration.
- **Leads:** unstable sort for equal priority; wildcard matching greediness; listener registered twice;
  lazy resolution per event; exception swallowing hiding failures.

## Exceptions
- **Classes:** `ApplicationNotAvailableException`, `ExceptionHandler`, `WhoopsServiceProvider`;
  `Bootstrapper/*`.
- **Role:** global error/exception handling and rendering.
- **Invariants:** handler stacks are balanced (no stacking across requests); production responses never
  leak internal messages/traces; previous exceptions chained; HTTP status mapping correct; Whoops only in
  debug; fatal/error-to-exception conversion consistent.
- **Leads:** Whoops registered in production; message leak in a fallback renderer; handler left installed
  after a request; nested exception losing the original.

## Facade
- **Classes:** `AbstractFacade`, `FacadeInterface`; `Bootstrapper/*`, `Exceptions/*`.
- **Role:** static proxy to container services.
- **Invariants:** the resolved instance is (re)bound correctly and cleared per request where required;
  the accessor exists; undeclared static calls fail loudly; no cross-request static state survives.
- **Leads:** cached instance surviving reset; facade returning a stale request-scoped object; magic
  `__callStatic` forwarding to `mixed`.

## Filesystem
- **Classes:** `File`, `Filesystem`, `FilesystemInterface`, `FilesystemMap`, `FilesystemMapInterface`;
  `Adapter/{Amazon,Ftp,Local,Memory,Sftp}`, `Contracts/*`, `Exception/*`, `Stream/*`, `Util/*`.
- **Role:** local/remote filesystem abstraction.
- **Invariants:** paths cannot escape the adapter root (traversal/symlink); remote failures surface as
  exceptions; writes are safe (atomic where claimed); MIME/extension checks are not trust-based alone;
  stream wrappers validate input; permissions are sane.
- **Leads:** `..`/absolute path escape in Local adapter; symlink following; predictable temp file; MIME
  from user input; missing error check on `fwrite`/`rename`.

## Http
- **Classes:** `HeaderCollection`, `Http`, `JsonResponse`, `MacroServiceProvider`, `RedirectResponse`,
  `Request`, `RequestFactory`, `Response`, `ResponseFactory`, `RoadRunnerResponderInterface`,
  `RoadRunnerWorker`, `StreamedResponse`, `Url`, `helper.php`; `Exceptions/*`, `Upload/*`.
- **Role:** HTTP kernel, request/response, headers, redirects, URL, uploads, RoadRunner bridge.
- **Invariants:** request parsing (superglobals vs PSR-7) is consistent; header handling is case-insensitive
  and multi-value safe; response status/headers/body encode correctly; redirects do not allow open-redirect
  of untrusted input; JSON encoding handles failures; uploads validate size/type/error codes; the RR
  bridge preserves every field and always resets.
- **Leads:** open redirect; header injection via newline; multi-value/`Set-Cookie` collapse; nested
  query/body arrays dropped; upload `tmp_name` from untrusted metadata.
- **Note:** the RoadRunner-specific deep dive (worker loop, per-request isolation, PSR-7 fidelity) is the
  `framework-roadrunner-audit` skill's job; cross-reference it instead of duplicating.

## Logging
- **Classes:** `LoggingManager`, `LoggingServiceProvider`, `Stream`; `Exception/*`, `Facade/*`.
- **Role:** PSR-3-style logging with channels/streams.
- **Invariants:** level filtering correct; PSR-3 method signatures honored; log injection (CR/LF) from
  context/messages neutralized; context is safely serialized (no fatal on circular/unknown); file
  permissions/rotation sane.
- **Leads:** newline injection; formatting of objects throwing; channel config ignored; no rotation.

## Macroable
- **Classes:** `MacroableTrait`; `Exceptions/*`.
- **Role:** runtime macro registration on classes.
- **Invariants:** macros are registered before use; macro closures bind as documented; static macro
  registries do not leak across requests/tests; existing methods are not silently overridden; `mixin`
  registers expected methods.
- **Leads:** static registry surviving reset; `__call` returning `mixed`; macro name colliding with a real
  method.

## Middleware
- **Classes:** `CsrfMiddleware`, `MaintenanceMiddleware`, `StartSessionMiddleware`, `ThrottleMiddleware`.
- **Role:** built-in middleware.
- **Invariants:** execution order and short-circuiting are correct; bypass/exempt conditions match docs;
  session start failures degrade safely; throttle keys are trustworthy (IP spoofing via `X-Forwarded-For`
  only behind a trusted proxy); maintenance mode honors its allow-list.
- **Leads:** trusting client-supplied forwarded headers; exempting too many paths; session cookie flags
  weak; maintenance allow-list bypass via path normalization.

## Queue
- **Classes:** `Job`, `QueueAdapterInterface`, `QueueManager`, `QueueServiceProvider`; `Adapter/*`,
  `Exception/*`, `Facade/*`.
- **Role:** dispatch/serialize jobs to adapters.
- **Invariants:** job payloads serialize/deserialize (incl. closures via serializable-closure) safely and
  are rejected if tampered; retry/backoff semantics; failed jobs retained; dispatch-after-commit works;
  unknown job class handled.
- **Leads:** unserializing untrusted payload without an allow-list; retry storming; lost job on adapter
  error; closure payload not integrity-checked.

## RateLimiter
- **Classes:** `RateLimit`, `RateLimiter`, `RateLimiterFactory`, `RateLimiterInterface`,
  `RateLimiterServiceProvider`; `Policy/*`.
- **Role:** rate limiting with policy and storage backend.
- **Invariants:** counter increment + check is atomic; window boundaries accurate; keys bind to the right
  identity; headers (`Retry-After`/remaining) correct; backend failure fails open or closed as documented.
- **Leads:** non-atomic check-then-incr race; key only by IP (spoofable); window reset drift; fail-open on
  store outage.

## Redis
- **Classes:** `Redis`, `RedisConnector`, `RedisInterface`, `RedisManager`, `RedisServiceProvider`.
- **Role:** Redis connection/manager.
- **Invariants:** connection reuse and reset; auth/TLS/db index applied; key prefix applied everywhere;
  serialization consistent; commands handle errors; pipeline/transaction semantics.
- **Leads:** prefix skipped in some methods; credentials logged; unserialize of stored payload;
  connection leak.

## Router
- **Classes:** `AbstractRouter`, `Route`, `RouteDispatcher`, `RouteGroup`, `RouteServiceProvider`,
  `RouteUrlBuilder`, `Router`, `RouterInterface`; `Attribute/*`, `Attribute/Route/*`, `Exceptions/*`.
- **Role:** route registration, matching, dispatch, URL generation, route cache.
- **Invariants:** pattern compilation handles params, optional params, constraints and escaping; route
  collisions and duplicate names are detected; the static table is reset and rebuilt correctly; route
  cache unserializes only allowed classes with depth limits; URL generation encodes params; 404/405
  distinct; group middleware/prefix/name merge correctly.
- **Leads:** greedy/ambiguous match order; unescaped regex from a user constraint; duplicate name silently
  overwritten; cache allowing unexpected classes; URL builder not encoding params.

## Security
- **Classes:** `Algo`, `Crypt`, `HashServiceProvider`, `helper.php`; `Exceptions/*`, `Facade/*`,
  `Hashing/*`.
- **Role:** encryption (Crypt), hashing, signatures.
- **Invariants:** encryption uses a sound AEAD/algorithm with a random IV/nonce per message; keys come
  from config, not defaults; signatures/`hash_equals` are constant-time; password hashing uses a modern
  algorithm with sane params; base64/URL-safe encoding correct; tampered ciphertext fails closed.
- **Leads:** IV reuse or static IV; `==` for MAC compare; weak/legacy algorithm default; missing
  authentication tag; deterministic encryption of sensitive data.

## Session
- **Classes:** `SessionBag`, `SessionManager`, `SessionServiceProvider`, `StorageInterface`;
  `Exceptions/*`, `Facade/*`, `Storage/*`.
- **Role:** session lifecycle, storage, flash data.
- **Invariants:** id is regenerated on privilege change (fixation); cookie flags (Secure/HttpOnly/SameSite)
  correct; flash data ages exactly once; stored values serialize safely; storage is locked/concurrency-safe;
  per-request reset is correct in a long-lived worker.
- **Leads:** no id regeneration; weak cookie flags; flash data surviving an extra request; unserialize of
  session payload; storage race.

## Testing
- **Classes:** `TestCase`, `TestJsonResponse`, `TestResponse`; `Traits/*`.
- **Role:** base test case and response assertions (used by the starter app, which subclasses `TestCase`).
- **Invariants:** the base class stays a stable extension point; response decoding/assertions are correct
  and side-effect free; global handlers are left balanced.
- **Leads:** base case depending on removed tooling; assertions mutating the response; handler leakage.

## Text
- **Classes:** `Str`, `Text`, `helper.php`; `Exceptions/*`.
- **Role:** string helpers (slug, random, truncate, contains, case ops).
- **Invariants:** multibyte/UTF-8 correct (no byte truncation); slug transliteration deterministic; random
  strings use a CSPRNG and the requested alphabet; regexes are delimited/safe; truncation preserves
  encoding.
- **Leads:** `substr` instead of `mb_substr`; `rand`/`mt_rand` for tokens; slug losing/duplicating chars;
  regex not `u`-aware.

## Time
- **Classes:** `Now`, `helper.php`; `Traits/*`.
- **Role:** current-time helpers and time arithmetic.
- **Invariants:** timezone explicit (not server default silently); mutable vs immutable expectations;
  parsing failures handled; date ranges inclusive/exclusive as documented.
- **Leads:** implicit timezone; DST arithmetic; static `now()` cached and not advancing.

## Validator *(extension-adjacent)*
- **Classes:** `Rule`, `ValidationCondition`, `Validator`, `helper.php`; `Contract/*`, `Messages/*`,
  `Rule/*`, `Traits/*`.
- **Role:** input validation rules, messages, filters.
- **Invariants:** each rule's pass/fail semantics match its name and message; nullable/optional handling
  consistent; nested/array and wildcard keys work; message interpolation escapes values; conditions
  (`when`/`sometimes`) compose correctly; custom rule contract honored.
- **Leads:** a rule accepting invalid input; numeric/string coercion surprises; wildcard `*` mismatch;
  messages injecting raw input (XSS in rendered output); condition evaluated after the rule.

## View
- **Classes:** `AbstractTemplatorParse`, `DependencyTemplatorInterface`, `InteractWithCacheTrait`,
  `Portal`, `Templator`, `TemplatorFinder`, `View`, `ViewServiceProvider`, `Vite`, `helper.php`;
  `Exceptions/*`, `Facades/*`, `Templator/*`.
- **Role:** Templator view engine, template finding, caching, Vite integration, view helpers.
- **Invariants:** `{{ }}` echo is escaped (no unintended XSS) and `{% raw %}` is safe; compiled template
  cache keys/invalidation are correct and cleared per request where needed; template names cannot escape
  the view root (traversal); directives cannot be injected via data; Vite manifest handling degrades
  gracefully when absent; portal/section/yield semantics correct.
- **Leads:** unescaped output in `{{ }}`; cache key collision; `../` in template name; Vite manifest
  injection; portal leaking across requests.

---

## Global helpers (`helper.php` in the composer `files` map)
Areas shipping helpers: Application, Collection, Environment, Http, Security, Text, Time, Validator, View.
Verify: no accidental global state, no function-name collisions, correct return contracts, and that each
file stays registered in `composer.json`.
