# Pest -> PHPUnit conversion checklist

Run through this per package. `ec35c6a` in the starter app is the worked example. Commands use
`vendor/bin/*` directly (composer is denied by permissions); edit `composer.json` but don't run composer.

## 1. Test tree

- [ ] Add `tests/TestCase.php` (base class; absorbs the `Pest.php` binding and any bootstrap). Do **not** add the
      starter's Whoops handler-stripping: vendor packages never mount Whoops (only the starter's app kernels do).
- [ ] Add a `namespace <Root>\<Area>;` + class to every converted test file.
- [ ] Convert `it`/`test` closures to `test*` methods; `beforeEach`/`afterEach` -> `setUp`/`tearDown`;
      `beforeAll`/`afterAll` -> static `setUpBeforeClass`/`tearDownAfterClass`.
- [ ] Convert `->with`/`dataset` -> `#[DataProvider]`/`#[TestWith]`; `->group` -> `#[Group]`; custom
      expectations and Pest global helpers -> TestCase methods/traits.
- [ ] Delete `tests/Pest.php`.
- [ ] Move/extend fixture exclusions to match the new tree; **do not touch fixture contents**.

## 2. composer.json

- [ ] `require-dev`: add `phpunit/phpunit ^13.3` explicitly (it was only transitive through Pest).
- [ ] Remove `pestphp/pest` and every `pestphp/pest-plugin-*` (incl. `pest-plugin-phpstan`,
      `pest-plugin-type-coverage`).
- [ ] Remove `config.allow-plugins.pestphp/pest-plugin`.
- [ ] Scripts: `test` -> `vendor/bin/phpunit` (keep the package's `XDEBUG_MODE=off`/`coverage` wrapper;
      keep/add `test-no-coverage` -> `vendor/bin/phpunit --no-coverage`).
- [ ] Replace `type-coverage` (`pest --type-coverage`) with `tomasvotruba/type-coverage` driven through
      PHPStan (see the starter's setup).
- [ ] `autoload-dev`: point the `Tests\` namespace at the chosen root.
- [ ] Keep the `repositories` VCS entry and the forked `phpunit/php-code-coverage` (`dev-omega as 14.3.1`).

## 3. phpstan.neon.dist

- [ ] Remove the `vendor/pestphp/pest-plugin-phpstan/extension.neon` include (plugin is gone).
- [ ] Keep `level: 10`, bootstrap and `excludePaths`; keep any package-specific services
      (framework has custom `Omega\PHPStan\Type\*` services) and documented inline suppressions.
- [ ] If the package ran `pest --type-coverage`, add the `tomasvotruba/type-coverage` extension and the
      100% return/param/property/constant requirement instead.

## 4. phpcs.xml.dist / phpunit.xml.dist

- [ ] phpcs: no Pest-specific change; keep PSR12, line limit 120 and fixture exclusions.
- [ ] phpunit: keep `bootstrap`, `cacheDirectory`, `executionOrder`, `beStrictAbout*`, `failOnRisky`,
      `failOnWarning` and the coverage output as they were.
- [ ] If `requireCoverageMetadata=true` (gettext), add `#[CoversClass(...)]`/`#[CoversNothing]` to every
      test, or explicitly relax that setting.

## 5. CI (.github/workflows)

- [ ] `tests.yml`: `vendor/bin/pest` -> `vendor/bin/phpunit`.
- [ ] `static-analysis.yml`: keep `vendor/bin/phpstan analyse --no-progress --error-format=github`.
- [ ] `coding-standard.yml`: keep `mkdir -p cache/phpcs && vendor/bin/phpcs`.
- [ ] Drop any Pest type-coverage step or convert it to the PHPStan type-coverage run.

## 6. Fixtures never to reformat / regenerate by hand

- framework: `tests/Unit/*/fixtures/*`, `src/Omega/Console/stubs`.
- gettext: `src/Omega/Gettext/Languages` (vendored data), `tests/Tests/Gettext/assets`,
  `tests/data.php`, `tests/data.json` (regenerated each run).
- serializable-closure: `tests/Fixtures`.
- Keep any intentional warning/risk suppressions documented in each package's README/AGENTS.md (e.g.
  framework `SizeTest`/`ChecksumTest`, gettext `CodeScannerTest`/`LoaderEdgesTest`).

## 7. Verification (green before commit)

```bash
vendor/bin/phpunit --no-coverage
mkdir -p cache/phpcs && vendor/bin/phpcs
vendor/bin/phpstan analyse --no-progress
# plus the package's coverage run if its `test` script used coverage
```

Commit **inside the package repo**: `Convert the test suite from Pest to PHPUnit`. The change is not
tracked by the starter app once `vendor/` is refreshed.
