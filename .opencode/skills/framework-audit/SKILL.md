---
name: Framework Audit
description: Deep-audit the entire omega-mvc/framework package — every component area (Application, Container, Config, Router, Http, Database, Console, View, Event, Cache, Session, Security, Queue, Validator, Filesystem, Archive, and the rest) — for correctness, security, lifecycle/state leaks, robustness and API-contract defects, then report every finding in exhaustive detail. Use when reviewing or hardening the whole framework, when asked for a full framework audit, or when hunting bugs across the engine as a whole (beyond the RoadRunner-specific concerns). This is a code/behaviour audit, NOT a linter or quality-gate pass.
---

# Framework audit — whole package

`vendor/omega-mvc/framework` (namespace `Omega\`, PSR-4 `src/Omega/`) is the Omega MVC engine: a
standalone Composer package with its own git repo, its own CI, and its own test suite. The
`omega-mvc/omega` starter app consumes it from `vendor/` and subclasses a few extension points. This
skill drives an **exhaustive audit of the whole engine** and produces a findings report; it is the
broad companion to the RoadRunner-focused `framework-roadrunner-audit` skill.

> Governing stance: **assume nothing, prove everything.** Every finding needs a `file:line`, the
> invariant it violates, a reproduction (a minimal script or a precise call sequence), and a suggested
> fix. A lead is not a bug until reproduced.

## Golden rules

- Read `vendor/omega-mvc/framework/AGENTS.md` first; it holds the package's conventions and quirks.
- Work from the package directory. `composer*` is denied by the project permissions — call tooling
  directly when you must.
- **This audit is not gate-driven.** Do **not** run or cite `phpstan`, `phpcs`, `phpunit`/`pest`,
  `type-coverage` or the CI workflows as the measure of correctness; that tooling is being removed and
  is irrelevant to this audit. Establish correctness by **reading the code** and, only when a
  behaviour must be confirmed, by running a **minimal standalone PHP script in `/tmp`** that exercises
  the class directly (no test framework).
- **Never restyle or rewrite** `tests/Unit/*/fixtures/**` or `src/Omega/Console/stubs` — they are not
  production code and are excluded by the package's configs.
- This is a separate git repo: changes belong to the package's repo, not the starter app.
- **Report before you rewrite.** The deliverable is an exhaustive findings list (confirmed vs lead),
  not a silent patch. Only edit code when the user explicitly asks, or after they pick items.
- Reproduce the specific target's spec/contract from its own code and docs; do not assume Laravel or
  any other framework's semantics.

## Scope

Audit **every** area and file under `src/Omega/` (and the shipped `helper.php` autoload functions).
The component-by-component checklist, per-area invariants and candidate leads live in
`references/component-map.md`; the cross-cutting lenses, severity rubric and report template live in
`references/cross-cutting-checks.md`.

Areas in scope: Application, Archive, Cache, Collection, Config, Console, Container, Cron, Csrf,
Database, DocBlockGenerator, Environment, Event, Exceptions, Facade, Filesystem, Http, Logging,
Macroable, Middleware, Queue, RateLimiter, Redis, Router, Security, Session, Testing, Text, Time,
Validator, View.

## Workflow

1. **Inventory.** Enumerate every class/dir under `src/Omega/` and every global helper in the
   composer `files` map. Confirm the component map has no missing area; add anything new.
2. **Per-area audit.** Walk `references/component-map.md` area by area. For each area: state its
   contract, trace its call paths, and check every listed invariant. Record confirmed defects and
   leads separately.
3. **Cross-cutting audit.** Apply every lens in `references/cross-cutting-checks.md` (security, state
   and lifecycle, error/failure paths, serialization safety, input handling, configuration,
   concurrency, resources, API stability, docs-vs-behaviour) across the whole package.
4. **Trace the seams.** Follow real entry points end to end — HTTP request, console invocation,
   queue/cron worker, route boot, service-provider registration — and note where contracts break or
   state leaks between calls/requests.
5. **Findings report.** Emit **every** finding using the report template: severity, `file:line`, the
   invariant at stake, the reproduction, the impact, and a suggested fix. Separate confirmed bugs
   from investigation leads. Rank by severity.
6. **Implement only when asked.** Apply the smallest correct change, add a focused regression check,
   and keep the package's style. Re-verify the specific behaviour you changed.
7. **Commit when asked.** Commit inside the package repo in the existing message style.

## Never assume — verify first

The candidate leads in `references/component-map.md` are **hypotheses**, not confirmed bugs. Reproduce
each one (minimal `/tmp` script or a precise, instrumented call sequence) and label it before touching
code. False positives and mis-scoped "fixes" are worse than an honest open lead.

## References

- `references/component-map.md` — every area: role, key classes, invariants to verify, candidate leads.
- `references/cross-cutting-checks.md` — audit lenses, severity rubric, reproduction method, report template.
