# Quality gates — omega-mvc/gettext

"Compliant" means every gate below is green **with the documented exclusions respected**. The package's
`AGENTS.md` is the summary; this file is the operative checklist. Run everything from the package directory.

## Commands

```bash
XDEBUG_MODE=off php vendor/bin/phpcs                    # PSR-12 (phpcs.xml.dist)
XDEBUG_MODE=off php vendor/bin/phpcbf --standard=PSR12  # autofix (respects the ruleset exclusions)
XDEBUG_MODE=off php vendor/bin/phpstan analyse          # level 10 (phpstan.neon.dist)
vendor/bin/pest --type-coverage                         # pest-plugin-type-coverage, 100% target
XDEBUG_MODE=off php vendor/bin/pest --no-coverage       # quick suite run
XDEBUG_MODE=coverage php vendor/bin/pest                # with coverage (DO NOT call under the harness:
                                                        # `pest --coverage*` is denied by permissions)
```

- Single file / filter: `vendor/bin/pest tests/Tests/Gettext/Loader/PoLoaderTest.php` or `--filter=<name>`
  (the suite is large, ~3400 tests — never run it to "look around" when a single file will do).
- There are **no** `lint`/`check`/`ci` composer scripts; run each gate.

## Thresholds and what "compliant" means

- **phpcs**: 0 errors, full PSR-12 minus the documented exclusions.
- **phpstan**: 0 errors at **level 10**. Do not remove documented inline suppressions without proof they are obsolete.
- **type coverage**: 100% (native parameter/return/property types plus precise docblocks — array shapes,
  generics, `@throws`).
- **tests**: all pass; the only acceptable warnings are the two intentional ones (below).
- **new production code** must carry explicit native types and match the existing docblock style.

## Configuration facts

- `phpstan.neon.dist`: level 10; `tmpDir cache/phpstan`; bootstrap `tests/constants.php`; paths `src` + `tests`;
  excludes `src/Omega/Gettext/Languages` and `tests/Tests/Gettext/assets`; includes `pest-plugin-phpstan`.
- `phpcs.xml.dist`: `PSR12`; `parallel=4`; cache `cache/phpcs/phpcs.json`; roots `src` + `tests`. Exclusions:
  `src/Omega/Gettext/Languages/*`; `PSR2.Classes.PropertyDeclaration` for `Translation.php` and `Translations.php`
  (PHPCS 4 cannot tokenize PHP 8.4 property hooks); `PSR1.Files.SideEffects` for `tests/bootstrap.php` and
  `tests/Tests/**`; `tests/Tests/Gettext/assets/*`, `tests/data.php`, `tests/data.json`.
  Note: the ruleset deliberately has **no** `vendor/*` exclude pattern (a relative one would match an enclosing
  `vendor/` when the package is nested in the starter app and exclude everything).
- `phpunit.xml.dist`: `bootstrap tests/bootstrap.php`; `cacheDirectory cache/phpunit.cache`;
  `requireCoverageMetadata=true` + `beStrictAboutCoverageMetadata=true` (every test needs
  `covers()`/`#[CoversClass]`/`#[CoversNothing]`); `failOnPhpunitDeprecation=true`; `failOnRisky=true`;
  `failOnWarning=false`; `displayDetailsOnTestsThatTriggerWarnings=false`; `pathCoverage=true` →
  `cache/coverage-report`.
- CI: `.github/workflows/tests.yml`, `coding-standard.yml`, `static-analysis.yml`. Check the workflow commands
  still match the scripts after any tooling change.

## Intentional exceptions (do not "fix")

- Two tests intentionally trigger warnings and are re-enabled in CI via
  `displayDetailsOnTestsThatTriggerWarnings` + `failOnWarning`:
  `Scanner\CodeScannerTest::testScanFileThrowsWhenFileIsUnreadable` and
  `Loader\LoaderEdgesTest::testUnreadableFilesThrow`.
- Vendored `src/Omega/Gettext/Languages/**` receives deprecation fixes only, never a restyle.

## Artifacts (all gitignored / disposable)

`cache/phpcs/phpcs.json`, `cache/phpstan/`, `cache/phpunit.cache`, `cache/coverage-report/`. Create
`cache/phpcs` and `cache/phpstan` (`mkdir -p`) when calling tools directly.
