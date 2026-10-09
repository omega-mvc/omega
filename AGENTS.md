# AGENTS.md — Omega MVC Starter App

`omega-mvc/omega` is the **starter application**, not the framework. It is a custom PHP 8.4+ MVC
framework — **not Laravel** — despite the Vite/Tailwind setup and the Artisan-like `php omega` CLI.
The framework itself is the Composer package `omega-mvc/framework`, installed at
`vendor/omega-mvc/framework/` (namespace `Omega\`). Code there is untracked here and is lost on
`composer update`; application code is `App\*`. The package ships its own independent Pest suite
under `vendor/omega-mvc/framework/tests/`.

## Scope

Omega is four cooperating codebases. Each is its own Composer package with its own `vendor/`,
its own git repository, and its own `.github/workflows`, so match every change to the right one:

| Part | Location |
| --- | --- |
| Starter app (`omega-mvc/omega`) | this project directory (`app/`, `routes/`, `config/`, `resources/`, `bootstrap/`, `tests/`) |
| Framework (`omega-mvc/framework`, namespace `Omega\`) | `vendor/omega-mvc/framework/` |
| Gettext (`omega-mvc/gettext`) | `vendor/omega-mvc/gettext/` |
| Serializable closure (`omega-mvc/serializable-closure`) | `vendor/omega-mvc/serializable-closure/` |

The three packages under `vendor/omega-mvc/` are installed dependencies, not files tracked by
this starter app: they carry their own commits, commands, tests, and CI, and edits there are
lost on `composer update`. Do application work in the project directory and framework/package
work inside that package; never assume one repo's rules, history, or tooling apply to another.

### Package playbooks

Each vendor package carries its own `AGENTS.md` with that package's exact commands and quirks — read it
before touching the package. OpenCode auto-loads the nested file when you work inside the package. Three
subagents map 1:1 to these packages:

| Package | Playbook | Subagent |
| --- | --- | --- |
| Framework | `vendor/omega-mvc/framework/AGENTS.md` | `framework` |
| Gettext | `vendor/omega-mvc/gettext/AGENTS.md` | `gettext` |
| Serializable closure | `vendor/omega-mvc/serializable-closure/AGENTS.md` | `serializable-closure` |

The vendor packages test with **Pest 5**, not the PHPUnit suite used in this app. The playbooks are not
tracked by this app: commit them in each package's own repo, or they vanish on `composer update`.

The whole project also pins a forked, modified `phpunit/php-code-coverage` — the
`omega-mvc/php-code-coverage` repo, `dev-omega` aliased as `14.3.1`, resolved through the
`repositories` VCS entry in `composer.json` — to turn off Xdebug artifacts. Treat it as
intentional: don't swap back to upstream or drop the alias.

## Setup

- `.env` is required to boot (CLI, HTTP, and the test suites all load it). It is gitignored and
  `composer install` does **not** create it — only `composer create-project` does. Use
  `cp .env.example .env`.
- Frontend assets are gitignored (`node_modules/`, `public/build/`, `public/hot/`). A fresh clone
  has no compiled CSS: run `npm install && npm run build` once (or `npm run dev` for HMR). The
  `vite` template directive degrades gracefully when no manifest/dev server exists.
- `composer.lock` and `package-lock.json` are deliberately gitignored. Validate with
  `composer validate --no-check-lock`, never by trusting a lockfile.

## Commands

```bash
composer test              # PHPUnit 13 (phpunit.xml.dist)
composer lint              # phpcs: PSR-12, scope app/ + tests/
composer fix               # phpcbf (auto-fix)
composer check             # lint + test  (does NOT run phpstan)
composer ci                # fix + check
vendor/bin/phpstan analyse # level 10 + type coverage; NOT in composer scripts
php omega                  # list all CLI commands
php omega serve            # PHP built-in dev server
```

- Run `phpstan` explicitly: `composer check` omits it, but CI fails on it (`static-analysis.yml`).
- PHPStan is level 10 with `tomasvotruba/type-coverage` demanding **100% return/param/property/
  constant type coverage**. New code needs explicit native types plus precise docblocks (array
  shapes, generics, `@throws`) or the build breaks.
- `phpcs`/`phpstan`/coverage write to gitignored `cache/`. PHPCS cannot create its own cache
  directory — `mkdir -p cache/phpcs` first (CI does this).
- Single test: `vendor/bin/phpunit tests/Tests/App/IndexControllerTest.php` (or `--filter method`).
- Add `--no-coverage` when no coverage driver is installed (CI has none and always passes it);
  `composer test` otherwise emits an HTML report into `cache/coverage-report/`.

## Tests

- **PHPUnit, not Pest** (the suite was converted; a phpcs comment still mentions Pest). Suite root
  is `./tests`; class tests live in `tests/Tests/**` under the `Tests\` namespace, so app suites are
  `Tests\App\*`. `tests/bootstrap.php` is unnamespaced.
- Test classes extend `Tests\TestCase`, which extends `Omega\Testing\TestCase` and strips the Whoops
  handlers the kernels register — otherwise `failOnRisky`/`failOnWarning` turn green tests red.
- Each suite boots the app in its own `setUp()` via `require dirname(__DIR__, 3) . '/bootstrap/app.php'`
  and assigns `$this->app`. `tests/Tests/App/EntryPointTest.php` is the exception: it extends
  `PHPUnit\Framework\TestCase` and drives `public/index.php` in a child process.
- Current app suites need no database.

## Architecture / conventions

- Entry points: `public/index.php` (HTTP) and `omega` (console). Both load `vendor/autoload.php`
  and `bootstrap/app.php`, which calls `Env::load()`, builds the `Application` container, and binds
  `App\Kernel\HttpKernel`, `App\Kernel\ConsoleKernel`, and `ExceptionHandler`. `HttpKernel` resolves
  routes and owns the 404/405 fallbacks.
- PSR-4: `App\` → `app/`, `Tests\` → `tests/Tests`, `Database\Seeders\` → `database/seeders/`.
- Routing: `routes/web.php` via `Router::get(...)`. Attribute routes (`#[Get]`, `#[Name]`,
  `#[Middleware]`) are declared on a service class and registered with `Router::register([...])`.
  `routes/schedule.php` holds cron entries.
- Controllers expose `handle()` and receive dependencies as type-hinted parameters (resolved by the
  container); return `Omega\Http\Response`/`JsonResponse`. Render with the `view()` helper.
- Views use the **Templator** engine: files end in `.template.php` and use `{% ... %}` tags
  (`extend`, `section`, `yield`, `vite`, `raw`). `{{ $var }}` is a plain PHP echo, not a template
  tag. Not Blade.
- Generators via `php omega make:*`: `model`, `controller`, `migration`, `seeder`, `view`,
  `middleware`, `provider`, `command`, `exception`. Also `migrate:*`, `db:*`, `route:*`, `config:*`,
  `view:*` (`view:cache`/`view:clear`/`view:watch`), `cron:*`, `queue:work`, `down`/`up`, `serve`.
