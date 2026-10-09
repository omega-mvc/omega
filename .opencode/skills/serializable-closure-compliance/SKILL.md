---
name: Serializable Closure Compliance
description: Audit and fix the omega-mvc/serializable-closure package so it complies with the project's quality gates (PSR-12, PHPStan level 10, 100% type coverage, tests/CI) and with the security/correctness requirements of closure serialization (HMAC integrity, fail-closed behaviour, RCE-safe deserialization, PHP round-trip fidelity). Use when maintaining, reviewing or extending vendor/omega-mvc/serializable-closure, or when asked to find and implement what is missing or wrong in that package.
---

# serializable-closure package compliance

`vendor/omega-mvc/serializable-closure` (`Omega\SerializableClosure\`) is a standalone library with its
own git repo. It is a namespace-renamed fork of `laravel/serializable-closure`, **hardened** (RCE guard,
HMAC fail-closed) and typed for PHP 8.4. The framework and the starter app consume it, so a regression
is high blast-radius: treat every change as security-sensitive.

This skill audits it against **two authorities only**, then implements what is missing or fixes what is wrong:

1. **Project quality gates** — the package's own `phpcs`, `phpstan` (level 10), 100% type-coverage and test/CI config.
2. **Security & serialization correctness** — HMAC integrity/authenticity, fail-closed signing, RCE-safe
   deserialization, PHP serialization semantics and round-trip fidelity.

**Laravel is NOT a correctness oracle.** The upstream `laravel/serializable-closure` may be used only for a
feature comparison (what it exposes / how it behaves); Omega is intended to be superior — more features,
full 100% test metrics, real PHPStan level 10 and 100% type coverage. Never "fix" Omega toward Laravel.

## Golden rules

- Read `vendor/omega-mvc/serializable-closure/AGENTS.md` first; it has the exact commands and intentional exceptions.
- Work from the package directory and run `vendor/bin/*` **directly** (`composer*` is denied by the project
  permissions). Prefix `XDEBUG_MODE=off` for phpcs/phpstan. The one exception: `composer test` here runs
  coverage, so use `XDEBUG_MODE=off php vendor/bin/pest --no-coverage` for a fast run.
- **Never restyle or analyse** `tests/Fixtures/**` (exact token/format expectations) and keep every documented
  exclusion intact. Do not remove the documented `@phpstan-ignore-next-line` suppressions — they are tool limits.
- **Never weaken a guard silently.** Any change touching `Serializers/Native`, `Serializers/Signed`,
  `Signers/Hmac` or `Support/ReflectionClosure` needs a regression test, and an explicit note on security impact.
- Fix production code only for a real gate/security/correctness defect; add a regression test for every fix.
- This is a separate git repo: commit there; the starter app does not track `vendor/`.
- **Report before you rewrite.** Produce a prioritised findings list (missing / wrong / untested) with evidence
  before editing. Only start editing when asked to fix, or after the user picks items.

## Workflow

1. **Baseline.** From the package directory run and record:
   ```bash
   mkdir -p cache/phpcs cache/phpstan
   XDEBUG_MODE=off php vendor/bin/phpcs
   XDEBUG_MODE=off php vendor/bin/phpstan analyse
   vendor/bin/pest --type-coverage
   XDEBUG_MODE=off php vendor/bin/pest --no-coverage
   ```
2. **Quality audit.** Compare each result with `references/quality-gates.md`: gate failures, missing native
   types, missing tests, CI mismatch, broken exclusions, stale README/composer claims.
3. **Security & correctness audit.** Walk `references/security-and-serialization-spec.md` area by area
   (signing, deserialization/RCE guard, stream, reflection/round-trip, binding/scoping) and record every gap
   against an explicit property.
4. **Findings report.** Output a prioritised list with severity, each item carrying `file:line`, the property
   at stake, and a suggested fix. Reproduce each candidate with a test or a minimal script and label it
   confirmed vs lead.
5. **Implement.** Apply the smallest correct change, add regression tests, re-run every gate touched.
6. **Verify and commit.** All gates green, commit inside the package repo in the existing message style.

## Never assume — verify first

The references list concrete **candidate** gaps (e.g. `setSecretKey('')`/`'0'` silently downgrading to
unsigned, the `SerializableInterface::__invoke()` signature mismatch, `ReflectionClosure` caches never reset,
the `ARRAY_RECURSIVE_KEY` leftover brand, the README/composer coverage contradiction). They are investigation
leads, not confirmed bugs: reproduce each with a test or minimal script before editing, and say so in the report.

## References

- `references/quality-gates.md` — exact gates, thresholds, exclusions, suppressions and CI.
- `references/security-and-serialization-spec.md` — property checklist per area plus candidate gaps.
