# Omega MVC - Agent Instructions

## Project Overview
Lightweight PHP 8.4+ MVC framework application (omega-mvc/omega). Uses Composer for PHP, npm/Vite for frontend.

## Key Commands

### PHP/Composer
```bash
composer install              # Install dependencies
composer test                 # Run PHPUnit tests
composer lint                 # Run PHP_CodeSniffer (PSR-12) on app/
composer fix                  # Auto-fix code style issues
composer check                # lint + test
composer ci                   # fix + check (full CI pipeline)
```

### Frontend (npm)
```bash
npm install                   # Install frontend deps
npm run dev                   # Vite dev server
npm run build                 # Production build
```

### CLI Tool (omega)
```bash
php omega serve               # Start dev server
php omega migrate             # Run migrations
php omega make:migration <name>
php omega make:model <name> --table-name <table>
php omega make:controller <name>
php omega make:view <name>
php omega view:cache          # Cache compiled templates
php omega config:cache        # Cache configuration
php omega route:cache         # Cache routes
```

## Project Structure
```
app/                    # Application code (PSR-4: App\)
  Http/Controllers/     # Controllers
  Kernel/               # HttpKernel, ConsoleKernel
  Middlewares/          # HTTP middlewares
  Models/               # Eloquent-like models
  Providers/            # Service providers
bootstrap/app.php       # Application bootstrap, registers kernels
config/                 # Configuration files (app, database, cache, etc.)
database/migrations/    # Migration files
public/index.php        # HTTP entry point
resources/              # Views, CSS, JS
routes/web.php          # Web routes
routes/schedule.php     # Scheduled tasks
storage/                # Logs, cache, compiled views
tests/                  # PHPUnit tests (PSR-4: Tests\)
  AbstractTestCase      # Base test case, boots app via bootstrap/app.php
vendor/                 # Composer dependencies
  omega-mvc/
    framework/          # Core framework (Omega\Application, Router, HTTP, Console, etc.)
    gettext/            # Translation support
    serializable-closure/ # Closure serialization
omega                   # CLI entry point (#!/usr/bin/env php)
```

## Testing
- Framework: PHPUnit 13+ with Omega's TestCase
- Config: `phpunit.xml.dist` (coverage to `cache/coverage-report/`)
- Run single test: `vendor/bin/phpunit tests/Unit/SpecificTest.php`
- Test env: `APP_ENV=testing` (set in phpunit.xml.dist)

## Code Style
- Standard: PSR-12 with exclusions (see `phpcs.xml.dist`)
- Line limit: 120 chars
- Excludes: `PSR1.Methods.CamelCapsMethodName.NotCamelCaps`, `PSR12.Files.FileHeader.IncorrectGrouping`
- Cache: `cache/phpcs/phpcs.json`

## Environment
- Copy `.env.example` → `.env` (auto-done on `composer install`)
- Required PHP extensions: iconv, mbstring, openssl, pcntl, pdo, posix, readline, simplexml
- Database: MySQL/MariaDB/PostgreSQL/SQLite

## Routing
- Traditional: `Router::get('/path', [Controller::class, 'method'])`
- Attribute-based: `#[Get('/path')]` on service methods, then `Router::register([Service::class])`
- Cache routes for production: `php omega route:cache`

## Important Notes
- PHP 8.4+ required (strict types declared everywhere)
- Framework package: `omega-mvc/framework` (v1.0+)
- Uses `Omega\Application\Application` container
- Kernel classes in `app/Kernel/` handle HTTP/Console requests
- View engine uses `{% %}` syntax (Templator)