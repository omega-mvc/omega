# Quality gates — omega-mvc/framework

Run from `vendor/omega-mvc/framework/`. `composer*` is denied by the project permissions, so call the
binaries directly. Unlike gettext/serializable-closure, the framework scripts do **not** prefix
`XDEBUG_MODE=off`; add it yourself for phpcs/phpstan and pass `--no-coverage` for fast test runs.

## Commands

| Gate | Composer script | Direct equivalent |
| --- | --- | --- |
| Tests | `composer test` | `vendor/bin/pest` (add `--no-coverage` when no driver) |
| Type coverage | `composer type-coverage` | `vendor/bin/pest --type-coverage` |
| Static analysis | `composer phpstan` | `XDEBUG_MODE=off php vendor/bin/phpstan analyse` (level 10) |
| Lint | `composer lint` (`composer phpcs`) | `vendor/bin/phpcs src/ tests/` |
| Lint fix | `composer phpcbf` | `vendor/bin/phpcbf` |
| Check | `composer check` | lint + test — **does not** run phpstan |
| CI | `composer ci` | fix + check |

- Single test / filter: `vendor/bin/pest tests/Unit/Http/RoadRunnerMultiRequestTest.php` or `--filter=...`.
- Unit tests are tagged `--group=unit` in `pest.php`. Coverage minimum is **80**.
- `phpcs`/`phpstan`/coverage write to `cache/`; create it with `mkdir -p cache/phpcs cache/phpstan` when
  invoking tools directly.

## Thresholds & config

- **PHPStan level 10** (`phpstan.neon.dist`): includes `pest-plugin-phpstan` plus two custom services
  (`Omega\PHPStan\Type\ReflectionFunctionAbstractReturnTypeExtension`,
  `Omega\PHPStan\Type\ValidatorMagicPropertiesClassReflectionExtension`); bootstraps `vendor/autoload.php`;
  excludes `tests/Unit/*/fixtures/*` and `src/Omega/Console/stubs`.
- **Type coverage 100%** (return/param/property/constant). New code needs native types + precise docblocks.
- **PHPCS**: PSR-12, `severity=10`, line limit 120, paths `src`+`tests`, cache `cache/phpcs/phpcs.json`,
  excludes `tests/Unit/[^/]+/fixtures/`.
- **PHPUnit/Pest**: `bootstrap tests/bootstrap.php`, `cacheDirectory cache/phpunit`, `APP_ENV=testing`,
  `OMEGA_TEST_MODE=light`, `pathCoverage=true` → HTML `cache/coverage-report`, `beStrictAboutOutputDuringTests`,
  `executionOrder depends,defects`.
- Two known **intentional** warnings: `tests/Unit/Filesystem/Util/SizeTest` and `ChecksumTest` install a
  temporary error handler for an expected `E_WARNING`. Do not "fix" them.
- `tests/Unit/*/fixtures/**` and `src/Omega/Console/stubs` must not be linted/analysed/restyled.

## CI

`.github/workflows/{tests,coding-standard,static-analysis}.yml` (+ `ci.yml`), triggered on `pull_request`
and on `push` to `main`. `static-analysis.yml` runs `phpstan analyse --no-progress --error-format=github`
with `memory_limit=-1` and **does** fail the build — `composer check` alone does not catch it.

## Repo

- AGENTS.md exists on disk but is **untracked**; commit package changes in the package repo.
- The starter app subclasses `Omega\Http\Http`, `Omega\Console\ConsoleApplication` and
  `Omega\Testing\TestCase` — keep those extension points stable.
