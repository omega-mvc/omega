# AGENTS.md — Omega MVC Starter App

## Operating Rules

1. When not sure of something, stop and ask instead of guessing.
2. The user is always right; the user's word is the sole source of truth.
3. When the user gives an order, execute it without arguing.
4. Modifications made by the user are always to be considered legitimate; never "fix" or "restore" them unprompted.

## Agent Scope of Competence

The agent's competence is strictly limited to:

- **omega-mvc/omega** — starter application
- **omega-mvc/framework** — main library
- **omega-mvc/gettext** — gettext implementation
- **omega-mvc/serializable-closure** — serializable closure implementation; if something needs fixing, fix it freely

## Package Agents and Delegation

The per-package agents live in `.opencode/agents/` and are the experts for
their own Composer package in `vendor/omega-mvc/`. Delegate to them when a task
concerns a specific package:

- **`framework`** — `vendor/omega-mvc/framework/` (main library). Its
  `AGENTS.md` holds deep framework-authoring conventions; the agent reads it
  before every task.
- **`gettext`** — `vendor/omega-mvc/gettext/` (GNU gettext localization/i18n).
- **`serializable-closure`** — `vendor/omega-mvc/serializable-closure/`
  (closure serialization/storage).

Delegation rule: when the conversation touches a package, delegate to that
package's agent (subagent) and let it handle the work; the main agent stays
responsible for the starter app (`omega`) and for integration between the
pieces. Treated like "if something needs fixing, fix it freely": within its
package each subagent has license to fix issues directly. Never work on a
package codebase without consulting its agent first (a thorough-boundary
guardrail).

Custom PHP 8.4+ MVC framework (`omega-mvc/omega`), NOT Laravel. The framework lives in
`vendor/omega-mvc/framework/` (its AGENTS.md has deep framework-authoring conventions). That
installed package ships its own full test suite (~680 test files under
`vendor/omega-mvc/framework/tests/`), independent from this app's `tests/`.
`composer.lock` and `package-lock.json` are gitignored.

## Commands

```bash
composer install           # post-root-package-install copies .env from .env.example
composer test              # Pest v5 (phpunit.xml.dist)
composer lint              # phpcs --standard=PSR12 app/  (tests are NOT linted)
composer fix               # phpcbf
composer check             # lint + test
composer ci                # fix + check
vendor/bin/phpstan analyse # level 10, paths app+tests; no composer script
npm run dev                # Vite HMR (public/hot gitignored)
npm run build              # emits hashed assets to public/build/ (gitignored)
```

Verify in order: `lint -> test -> phpstan` (or `composer ci`). Single test:
`vendor/bin/pest tests/Feature/IndexControllerTest.php`

## CLI (`php omega <cmd>`)

`serve`, `migrate` (alias of `migrate:run`), `db:create`, `db:seed`,
`make:migration|make:model|make:controller|make:seeder|make:view|make:middleware|make:provider|make:command`,
`route:list|route:cache|route:clear`, `config:cache|config:clear`, `view:cache|view:clear`, `cache:clear`.

## Structure / Conventions

- Entry points: `public/index.php` (HTTP), `omega` (console). Both load `vendor/autoload.php` + `bootstrap/app.php`, which builds the container and registers `App\Kernel\HttpKernel` / `ConsoleKernel`.
- PSR-4: `App\` → `app/`, `Tests\` → `tests/`, `Database\Seeders\` → `database/seeders/`.
- Views: **Templator** engine, files end `.template.php` under `resources/views/`. Syntax is `{% ... %}` (extend/section/yield/vite), NOT Blade. `{{ $var }}` is PHP echo, not a template tag.
- Routing: `routes/web.php` via `Router::get(...)`; API-style uses method attributes (`#[Get]`, `#[Middleware]`) on a services class registered with `Router::register([...])`. `routes/schedule.php` = cron.
- Framework namespaces are `Omega\*`; app code is `App\*`. Routing/attributes/middleware come from the installed package, not this repo.
- Controllers type-hint dependencies and expose a `handle()` method; tests boot the app via `tests/AbstractTestCase` -> `bootstrap/app.php`.

## Testing

- App suites (Pest v5, `App\Tests` -> `tests/`): `APP_ENV=testing` set in `phpunit.xml.dist` and `tests/Pest.php`; phpstan config is level 10. Current `tests/Feature` files need no database.
- Framework suite (`vendor/omega-mvc/framework/tests/`): run from that directory (`vendor/bin/pest` inside the package). `tests/Unit/Database/RealDatabase/` runs every DB test once per engine via the `ManagesDatabase` trait (`engineProvider()` data set: mysql, mariadb, pgsql, sqlite); an engine that is not installed or unreachable **skips** rather than failing. Connection settings come from `ManagesDatabase::getConfiguration()` — overridable via `OMEGA_TEST_DB_HOST|USERNAME|PASSWORD|PORT` env (defaults: 127.0.0.1, root, `vb65ty4`, 3306; pgsql port 5432). Databases: `testing_db`, `testing_db_mariadb`, `testing_db_pgsql`, sqlite `:memory:`.