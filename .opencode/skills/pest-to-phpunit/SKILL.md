---
name: Pest to PHPUnit
description: Convert an Omega codebase's Pest 5 test suite to PHPUnit 13 — rewrite it/test/expect/datasets/hooks/groups into PHPUnit classes and attributes, then swap composer scripts, phpstan/type-coverage and CI. Use when migrating tests off Pest, or when working in a vendor package (framework, gettext, serializable-closure) that still ships Pest.
---

# Pest -> PHPUnit conversion

Omega is four cooperating codebases (see the root `AGENTS.md`). The **starter app is already PHPUnit**;
its conversion is commit `ec35c6a` and is the canonical example to imitate. The three vendor packages
still run **Pest 5** and each is its own git repo:

- `vendor/omega-mvc/framework` (namespace `Omega\`, suite under `tests/`)
- `vendor/omega-mvc/gettext` (namespace `Omega\Gettext\`, suite under `tests/Tests/`)
- `vendor/omega-mvc/serializable-closure` (namespace `Omega\SerializableClosure\`, suite under `tests/`)

Each package is an independent git repo with its own `vendor/` and its own caches, so the packages can be converted **in parallel** (one worker per package) from the starter workspace; commit inside each package's own repo.

## Golden rules

- Read the target package's own `AGENTS.md` first; it has the package-specific commands and exceptions.
- Use `vendor/bin/phpunit`, `vendor/bin/phpstan`, `vendor/bin/phpcs` **directly** — `composer` is denied by
  `.opencode/opencode.jsonc` permissions. You may edit `composer.json`; just run the binaries yourself.
- Do not use `composer update`/`install`: it would overwrite the package (untracked under `vendor/`) and lose
  your work. Edit files in place and commit in the package repo.
- **Never reformat fixtures, snapshots or vendored data.** See `references/conversion-checklist.md`.
- Convert **test files only**. Touch production code only if a real bug surfaces (as in `ec35c6a`, where a
  commented-out `return` in `AppMiddleware::handle()` was hiding every call behind a `TypeError`).
- A converted suite must no longer require `pestphp/*` in `require-dev`, and `tests/Pest.php` must be gone.
- These are vendor packages: a skill under the starter `.opencode/skills` is only discovered while the session's
  project root is the starter. If you open a package as its own project, use a global skill or a `skills` catalog.

## Workflow

1. **Recon.** Read `tests/Pest.php`, `phpunit.xml.dist`, the `composer.json` scripts, `phpstan.neon.dist`,
   `phpcs.xml.dist` and `.github/workflows/*.yml`. Count the Pest idioms (see the map's census) so nothing is missed.
2. **Pick the namespace/root.** Each test file gains `namespace <Root>\<Area>;` and a class. Choose the
   `autoload-dev` root and update it (starter moved `Tests\` from `tests/` to `tests/Tests`, classes in `tests/Tests/App`).
3. **Create the base TestCase.** Add `tests/TestCase.php` extending the package's test base (or `PHPUnit\Framework\TestCase`),
   absorbing `Pest.php`'s `uses()`/`pest()->extend()` binding and bootstrap. Turn Pest global helper functions into
   static methods/traits, and `expect()->extend(...)` custom expectations into assertion methods. Do **not** port the
   starter's Whoops handler-stripping (see the gotcha below): only the starter's app kernels mount Whoops.
4. **Convert files** mechanically with `references/pest-to-phpunit-map.md`: add namespace + class + `test*` methods;
   `beforeEach`/`afterEach` -> `setUp()`/`tearDown()`; `beforeAll`/`afterAll` -> static `setUpBeforeClass()`/
   `tearDownAfterClass()`; datasets -> `#[DataProvider]` / `#[TestWith]`; groups -> `#[Group]`; `CoversClass` when
   the suite sets `requireCoverageMetadata=true`.
5. **Delete `tests/Pest.php`.**
6. **Migrate config/CI** (composer, phpstan, phpunit.xml, workflows) — checklist in `references/conversion-checklist.md`.
7. **Verify** in the package directory, with binaries directly:
   ```bash
   vendor/bin/phpunit --no-coverage
   mkdir -p cache/phpcs && vendor/bin/phpcs
   vendor/bin/phpstan analyse --no-progress
   ```
   Also run the package's coverage command if its `test` script used coverage. All must be green before committing.
8. **Commit** in the package repo, message style `Convert the test suite from Pest to PHPUnit`.

## Gotchas that change meaning

- PHPUnit 13 has **no `assertThrows` and no `assertArraySubset`** (verified against the installed 13.3).
  `toThrow` -> `expectException*`; `toMatchArray` -> compare a filtered subset or assert key-by-key.
- `toBe` is identity -> `assertSame`; `toEqual` is loose -> `assertEquals`. Note PHPUnit reverses the argument
  order (`assertSame($expected, $actual)`).
- `toContain` is overloaded: string -> `assertStringContainsString`, array -> `assertContains`.
- Pest `$this` inside closures is the TestCase; class methods keep working, but closures become methods (watch
  `use` scope and `static` data providers).
- `failOnRisky`/`failOnWarning`: tests that install their own temporary error handlers (`set_error_handler`)
  must restore them, and keep each package's documented intentional warning suppressions. The starter's
  `Tests\TestCase` Whoops-stripping is **starter-only** — the app kernels `App\Kernel\HttpKernel`/
  `ConsoleKernel` are what call `Whoops\Run::register()`. The framework's `WhoopsServiceProvider` only *binds*
  the Whoops objects into the container, and the vendor packages' tests never boot those kernels, so do not copy
  the Whoops-stripping hack into a vendor package's converted base TestCase.
- gettext sets `requireCoverageMetadata=true` + `beStrictAboutCoverageMetadata`: every test needs
  `#[CoversClass(...)]` or `#[CoversNothing]`.
- The forked `omega-mvc/php-code-coverage` (`dev-omega` as `14.3.1`, via `repositories` VCS) must stay; do not
  swap to upstream or drop the alias.

## References

- `references/pest-to-phpunit-map.md` — full idiom -> PHPUnit mapping and API table.
- `references/conversion-checklist.md` — per-file config/composer/CI/fixtures checklist.
