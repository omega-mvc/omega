# Omega MVC - Agent Instructions

Lightweight PHP 8.4+ MVC (omega-mvc/omega). Composer + npm/Vite. `composer.lock` and `package-lock.json` are gitignored.

## Verified Commands

### PHP
```bash
composer install
composer test                 # Pest v5
composer lint                 # phpcs --standard=PSR12 app/
composer fix                  # phpcbf
composer check                # lint + test
composer ci                   # fix + check
vendor/bin/phpstan analyse    # level 10, no composer script
```

Order when verifying: `lint -> test -> phpstan` (or `composer ci`).

### Frontend
```bash
npm install
npm run build                 # emits to public/build/ (gitignored)
npm run dev                  # Vite HMR; public/hot/ gitignored
```
Layout loads assets via `vite` directive (`resources/views/base/base.template.php`).

### CLI (`omega`)
```bash
php omega serve
php omega migrate
php omega db:create           # creates DB (README only)
php omega make:migration <name>
php omega make:model <name> --table-name <table>
php omega make:controller <name>
php omega make:view <name>
php omega make:seed <name>     # framework has MakeSeed
php omega seed                 # framework has Seed command
php omega view:cache | config:cache | route:cache
php omega route:list
```

## Structure That Changes Behavior

- `bootstrap/app.php` boots app; `tests/Feature/` extends `Tests\AbstractTestCase` which boots via it.
- PSR-4: `App\` -> `app/`, `Tests\` -> `tests/`. `Database\Seeders\` -> `database/seeders/`.
- Entry points: `public/index.php` (HTTP), `omega` (console).
- Framework lives in `vendor/omega-mvc/framework/` (not `app/`).

## Testing / Verification
- Pest v5, config: `phpunit.xml.dist` (`APP_ENV=testing`, coverage -> `cache/coverage-report/`).
- Single test: `vendor/bin/pest tests/Feature/IndexControllerTest.php`.

## Routing & Views
- Traditional: `Router::get('/path', [Controller::class, 'method'])`.
- Attribute: `#[Get('/path')]` on method, then `Router::register([Service::class])`.
- View engine: **Templator** (`{% %}` syntax), not Blade (`{{ }}`).

## Env / Config
- `.env` copied from `.env.example` automatically by `composer install` (`post-root-package-install`).
- Required extensions: iconv, mbstring, openssl, pcntl, pdo, posix, readline, simplexml.

## Opencode Permissions (`.opencode/`)
- `vendor/bin/phpstan*` allowed; `vendor/bin/phpcs*` and `vendor/bin/phpunit*` ask; `composer*` ask.
