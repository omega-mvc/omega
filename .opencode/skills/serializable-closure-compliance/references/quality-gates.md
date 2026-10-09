# Quality gates — omega-mvc/serializable-closure

Run from `vendor/omega-mvc/serializable-closure/`. `composer*` is denied by the project permissions, so call
the binaries directly. All scripts except `type-coverage` and `test` prefix `XDEBUG_MODE=off`.

## Commands (source of truth = composer.json scripts)

| Gate | Composer script | Direct equivalent |
| --- | --- | --- |
| Lint | `composer phpcs` | `mkdir -p cache/phpcs && XDEBUG_MODE=off php vendor/bin/phpcs` |
| Lint fix | `composer phpcbf` | `XDEBUG_MODE=off php vendor/bin/phpcbf --standard=PSR12` |
| Static analysis | `composer phpstan` | `XDEBUG_MODE=off php vendor/bin/phpstan analyse` |
| Type coverage | `composer type-coverage` | `vendor/bin/pest --type-coverage` |
| Tests (fast) | `composer test-no-coverage` | `XDEBUG_MODE=off php vendor/bin/pest --no-coverage` |
| Tests + coverage | `composer test` | `XDEBUG_MODE=coverage vendor/bin/pest --coverage` |

There are **no** `lint`/`check`/`ci` scripts. `composer test` runs coverage here (unlike framework/gettext).

- Single file / filter: `vendor/bin/pest tests/Unit/SerializableClosureTest.php`, `--filter=round-trips`.
- Stress only: `vendor/bin/pest --group=stress` (budget below). Override: `OMEGA_STRESS_ITERATIONS=500`.

## Thresholds & gotchas

- **PHPStan level 10** (`phpstan.neon.dist`), includes `pest-plugin-phpstan`, `tmpDir cache/phpstan`,
  paths `src`+`tests`, excludes `tests/Fixtures`. Must exit 0.
- **Type coverage 100%** via `pest-plugin-type-coverage`. Any new code needs explicit native types plus
  precise docblocks.
- **PHPCS**: PSR-12, `parallel=4`, cache `cache/phpcs/phpcs.json`, files `src`+`tests`, excludes
  `tests/Fixtures/*`. Disables `PSR1.Files.SideEffects` for `tests/*` and `PSR1.Methods.CamelCapsMethodName`
  for `src/Omega/SerializableClosure/Support/ClosureStream.php` (stream wrapper). **No** `vendor` exclude —
  a nested `vendor/` pattern would exclude the whole package.
- **PHPUnit/Pest strictness**: `failOnRisky=true`, `failOnWarning=true`, `pathCoverage=true` with HTML to
  `cache/coverage/`, `OMEGA_STRESS_ITERATIONS=10`, `displayDetailsOnTestsThatTrigger*` all true.
- **Stress budget** (`Tests\TestCase::stressIterations()`): `OMEGA_STRESS_ITERATIONS` as-is >
  `OMEGA_TEST_MODE=light` (10) > `CI`/`GITHUB_ACTIONS` (100) > local (10000); skipped when ≤ 1.
- Add `--no-coverage` when no coverage driver is installed; Xdebug ≤ 3.4.5 can crash *after* a green
  path-coverage run (upstream bug) — rerun or split by file, don't chase it.

## Documented exceptions — keep them

- Inline `@phpstan-ignore-next-line` suppressions with identifiers, explained in README; `composer phpstan`
  exits 0. Two classic false positives in `tests/Unit/Support/ReflectionClosureTest.php`
  (`callable.nonCallable`, `function.inner`); five `pest.expectation.impossible` in
  `tests/Unit/Serializers/NativeTest.php` (by-ref mutation through `ReflectionMethod::invokeArgs`).
- Coverage honesty (README): line **100%**, branches **~97–99%** (driver attribution noise), paths **<1%** —
  `ReflectionClosure::getCode()` exposes 4096 (2^12) paths **by design**; functions/methods & classes follow
  the path metric. Never quote a single-run figure as exact.

## CI & repo

- `.github/workflows/{ci,coding-standard,tests,static-analysis}.yml`.
- AGENTS.md exists on disk but is **untracked**; commit package changes in the package repo.

## Candidate quality/doc gaps (leads)

- `composer test` runs coverage, but README says a plain `composer test` "skips coverage on purpose" — contradiction.
- README documents `phpdoc.xml.dist` / `cache/apiDoc`, but commit `0c31a41 Remove phpdoc configuration` deleted it.
- `Serializers/Native::ARRAY_RECURSIVE_KEY = 'OMEGACMS_SERIALIZABLE_RECURSIVE_KEY'` — leftover legacy brand.
