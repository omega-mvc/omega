---
name: Framework RoadRunner Audit
description: Deep-audit the Omega framework components that are critical for running under RoadRunner (rr) persistent workers — per-request state isolation, the PSR-7 request/response bridge, the worker loop, route re-registration, long-lived DB connections and error-handler reset — and report every finding in exhaustive detail. Use when reviewing or hardening vendor/omega-mvc/framework for RoadRunner, when a request leaks state into the next one, or when asked to audit the worker/HTTP lifecycle.
---

# Framework audit — RoadRunner-critical components

`vendor/omega-mvc/framework` is built around **RoadRunner** (binary `rr`; server `spiral/roadrunner-http`
plus a PSR-7 implementation are application-level deps — the framework itself only requires the
`psr/http-message` and `psr/http-factory` **interfaces**). The whole design assumes the application boots
**once** and serves many requests: `RoadRunnerWorker::run()` converts each PSR-7 request to an Omega
`Request`, handles it, converts the response back, and calls `Http::terminate()` to reset per-request state.

> The invariant that governs this audit: **no state may survive from one request to the next unless it is
> explicitly process-lifetime.** Every finding is a violation of that, a PSR-7 fidelity break, or a failure
> path that skips the reset.

## Golden rules

- Read `vendor/omega-mvc/framework/AGENTS.md` first; it has the package's exact commands and exceptions.
- Work from the package directory and run `vendor/bin/*` **directly** (`composer*` is denied by the project
  permissions). Framework scripts do **not** prefix `XDEBUG_MODE=off`; add `XDEBUG_MODE=off` when running
  phpcs/phpstan directly, and pass `--no-coverage` for fast test runs.
- **Never restyle or analyse** `tests/Unit/*/fixtures/**` and `src/Omega/Console/stubs` (excluded by config).
  Keep every documented exclusion and phpstan suppression intact.
- Any change to a reset path, the worker loop, or the PSR-7 bridge needs a regression test and an explicit
  note on state-leak / security impact.
- This is a separate git repo: commit there; the starter app does not track `vendor/`.
- **Report every detail before you rewrite.** The deliverable is an exhaustive findings list (confirmed vs
  lead), not a silent patch. Only edit when asked, or after the user picks items.

## Workflow

1. **Baseline.** From the package directory run and record:
   ```bash
   vendor/bin/pest tests/Unit/Http/RoadRunnerMultiRequestTest.php --no-coverage
   XDEBUG_MODE=off php vendor/bin/phpstan analyse
   XDEBUG_MODE=off php vendor/bin/phpcs
   vendor/bin/pest --no-coverage
   ```
2. **Component inventory.** Walk every entry in `references/roadrunner-critical-components.md` and confirm
   it exists, is wired, and behaves as documented. Flag missing/undeclared pieces.
3. **Per-request lifecycle trace.** Follow one request end to end and mark the exact reset boundary:
   `RoadRunnerResponderInterface::waitRequest()` → `RequestFactory::fromPsr7ServerRequest()` →
   `Http::handle()` → dispatcher/middleware → `Response` → `ResponseFactory::toPsr7()` →
   `responder->respond()` → `Http::terminate()` → `resetForRequest()`.
4. **Isolation / leak audit.** Boot the app once and drive **many** requests (a fake responder in the style
   of `Spiral\RoadRunner\Http\PSR7Worker`, and/or the existing `RoadRunnerMultiRequestTest` harness) with
   `OMEGA_TEST_MODE=light`. After each request assert: no container singleton captured the previous request,
   static `Router`/`Facade` state is clean, the Templator dependency map is empty, no DB transaction or log
   survives, error/exception handler stacks are balanced, terminate callbacks are drained, and the route
   table is repopulated.
5. **Failure-path audit.** Force throws at each stage (make request, handle, convert response, respond) and
   verify the worker still resets and emits a safe response (no internal message leak).
6. **PSR-7 fidelity audit.** Compare every field the bridge copies (method/override, URI, headers incl.
   multi-value/`Set-Cookie`, query/body/cookies/files, protocol version, status line) against the source.
7. **Findings report.** Output **every** finding: severity, `file:line`, the invariant at stake, the
   reproduction, and a suggested fix. Separate confirmed bugs from investigation leads.
8. **Implement & verify.** Smallest correct change + regression test; re-run the baseline; commit in the
   package repo in the existing message style.

## Never assume — verify first

The component reference lists concrete **candidate** gaps (error-response message leak, `terminate()`
skipped when a request throws, `bootedCallbacks` cleared each request, `Set-Cookie`/multi-value header
collapse, dropped nested query arrays, upload `tmp_name` from stream metadata, route-cache unserialize
allow-list, memory growth in `MemoryStorage`/`ReflectionClosure`). They are leads, **not** confirmed bugs:
reproduce each with a test or minimal script and label it before changing code.

## References

- `references/roadrunner-critical-components.md` — component map, per-component invariants, candidate gaps.
- `references/quality-gates.md` — framework gates, thresholds, exclusions and CI.
