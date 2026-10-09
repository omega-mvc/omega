# Cross-cutting audit lenses

Apply every lens below across the whole package (`src/Omega/`). These are independent of any linter or
test tooling: they are questions you answer by reading code and, when needed, by running a **minimal
standalone PHP script in `/tmp`** against the class.

## How to reproduce a finding

- Prefer the smallest direct call: instantiate the class (or resolve it from a hand-built container) and
  drive the exact inputs that trigger the defect. No test framework required.
- Capture the actual observed output/exception and compare it to the documented contract.
- If a defect needs an app boot, boot the framework's `Application` directly with a throwaway config;
  do not depend on the starter app's suites.
- Record the PHP version and any driver/config used; behaviour can be driver- or SAPI-specific (CLI vs
  FPM vs RoadRunner).

## Lens 1 — Correctness & contract

- Does each public method do what its name/signature/docblock promises? Compare against the package's own
  docs and usage in `src/`, not against other frameworks.
- Return types and nullability: can a documented non-null return actually return null (and vice-versa)?
- Boundary values: empty string/array/collection, `0`, `null`, negative numbers, very large inputs,
  unicode, duplicate keys, missing keys.
- Off-by-one in ranges/pagination/truncation/windows.
- Equality: `==` vs `===`, string vs int keys, float comparison.

## Lens 2 — Security

- **Injection:** SQL identifiers/values, LIMIT/OFFSET, `LIKE` wildcards; shell/`exec`; header injection
  via CR/LF; template/data injection; log injection.
- **Path traversal:** filesystem adapters, archive extraction (zip-slip), template finding, file writes,
  stream wrappers, generated files.
- **Deserialization:** every `unserialize`/`allowed_classes`, phar metadata, route/job/config cache,
  session/cache payloads, closure serialization (allow-list + integrity check; fail closed).
- **Crypto:** algorithm choice, IV/nonce per message, authentication tag, key source/defaults, base64
  variants, constant-time comparison (`hash_equals`) for MACs/tokens/CSRF.
- **Authn/z primitives:** session id regeneration, CSRF token CSPRNG + rotation, password hashing params,
  rate-limit key trust, trusted-proxy handling for forwarded IP/proto/host.
- **Information disclosure:** production error rendering, debug flags, logs, responses containing internal
  paths/messages/SQL.
- **Redirects/URLs:** open redirect via untrusted target, scheme/host validation, URL generation encoding.

## Lens 3 — State & lifecycle

- The engine can run **one process for many requests** (RoadRunner) and long-lived workers (queue/cron).
  Find any state that survives when it must not: statics, facades, container singletons holding request
  data, `ReflectionCache`, macro registries, event listeners, templator dependency maps, route tables,
  error/exception handler stacks, DB transactions/logs.
- Conversely: state that **must** persist but is cleared each request (config, providers, schedule).
- Reset symmetry: every "install"/"register"/"push" has a matching "reset"/"deregister"/"pop"; every
  `set_error_handler`/`set_exception_handler` is balanced.
- Re-entrancy: calling boot/handle/reset twice must not duplicate registrations or lose them.

## Lens 4 — Error handling & failure paths

- Every `catch`, `finally` and cleanup: does the failure path still release resources and restore global
  state? Does it swallow errors silently or leak internals?
- Partial failure during a multi-step operation (write/rename, transaction, extraction, plugin/provider
  registration) — is state left consistent?
- Exceptions thrown from user callbacks (listeners, macros, rules, middleware) — isolated or propagated as
  documented?
- Return-vs-throw consistency for the same error condition across the package.
- `null`/`false` error returns checked by callers.

## Lens 5 — Input handling & validation

- Trust boundaries: request data, `.env`, files, archives, remote adapter responses, headers, cookies,
  uploaded files, route params, query strings.
- Validate type, range, shape and encoding before use; reject rather than coerce silently when security
  relevant.
- Nested/array/wildcard input handling (query strings, validation keys) matches the documented shape.

## Lens 6 — Configuration & environment

- Config defaults are safe (secure-by-default), and required secrets have no insecure fallback.
- Precedence between defaults, config files, env and runtime overrides is explicit and consistent.
- Missing/invalid config produces a clear error, not a silent wrong behaviour.
- Config caching doesn't serialize non-serializable values or drift from source.

## Lens 7 — Concurrency & long-running processes

- Shared mutable state across worker iterations; read-modify-write races in counters, caches, sessions,
  rate limiters.
- Locks/atomicity for schedules and job claiming; idempotency of retried work.
- Timeouts/keepalive for connections; reconnect after the backend drops.
- Memory: caches/registries that only grow; recursion depth; unbounded buffers.

## Lens 8 — Resource use & performance

- Unbounded memory (buffering whole files/responses/archives), N+1 queries/loops, repeated reflection,
  redundant I/O, O(n²) scans in hot paths.
- Leaked handles (files, sockets, PDO) and missing `finally` closes.
- Caching where warranted, invalidation where cached.

## Lens 9 — API contract & extension points *(high stakes)*

- The starter app subclasses `Omega\Http\Http`, `Omega\Console\ConsoleApplication` and
  `Omega\Testing\TestCase` (and implements/extends others). Treat their signatures, protected hooks and
  documented behaviour as **public API**: flag breaking changes and undefined behaviour.
- Service-provider and bootstrapper extension points: ordering guarantees, what subclasses may safely
  override, and whether overrides are actually called.
- Facade/helper contracts consumed by the app.

## Lens 10 — Documentation vs behaviour

- README/docblocks/AGENTS.md vs actual code. Flag contradictions (they cause misuse). Do not "fix" docs
  to match a bug — report the mismatch and pick the correct authority.

## Severity rubric

| Severity | Meaning |
| --- | --- |
| **Critical** | Remote exploit, secret/key exposure, authn/z bypass, arbitrary code execution, data loss. |
| **High** | Security weakness, guaranteed correctness bug in a common path, per-request state leak, break of a starter-app extension point. |
| **Medium** | Bug in an edge path, error-handling/resource gap, config/security default issue. |
| **Low** | Cosmetic, doc mismatch, minor inefficiency. |
| **Lead** | Unverified suspicion — must be reproduced before it can be rated. |

## Findings report template

For **each** finding, emit:

```
[SEVERITY][CONFIRMED|LEAD] <area>/<short title>
File: src/Omega/<Area>/<File>.php:LINE
Invariant: <the contract or rule violated>
Repro: <minimal steps/script + observed result>
Impact: <what breaks, security/data/behaviour>
Fix: <smallest correct change; note regression risk>
```

End the report with:
- a **summary table** (severity × area) sorted by severity;
- the **confirmed vs lead** split;
- any **open questions** that block a verdict;
- the exact **artefacts** you used to reproduce (script paths under `/tmp`).

## Anti-patterns to avoid in this audit

- Do not treat `phpstan`/`phpcs`/`phpunit` output as the audit's source of truth; verify behaviour directly.
- Do not edit code, fixtures or stubs during the audit phase — report first.
- Do not generalise from one driver/SAPI (CLI vs RoadRunner) without checking the other.
- Do not invent candidate bugs as facts; label leads as leads.
