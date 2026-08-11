# AGENTS.md - Progetto Omega

## Project Root & Scope
- La root del progetto corrisponde alla directory corrente (`.`) in cui risiede questo file.
- **Perimetro di analisi:** L'agente deve considerare parte attiva del contesto unicamente il codice sorgente applicativo contenuto in (`vendor/omega-mvc/`).
- **Esclusioni:** Tutta la restante parte della cartella `vendor/` e il contenuto di `node-modules`  devono essere ignorati per ottimizzare le risorse e il contesto.

## Regole Operative e Linguaggio
1. **Lingua:** Qualsiasi file `.md` o documentazione generata dall'agente (incluso un eventuale sotto-file `AGENTS.md`) deve essere rigorosamente scritto in **italiano**.
2. **Gestione del file AGENTS.md:** L'agente non deve sovrascrivere o ignorare questo file, ma eseguire un'operazione di **merge** e aggiornamento incrementale, preservando le configurazioni e le regole esistenti qui definite.
3. Concentrare le analisi e le modifiche unicamente sui file inclusi nello scope autorizzato.
4. Trattare i percorsi come relativi alla root del progetto.

## Panoramica
- App starter del framework **Omega** (PHP ^8.4, PSR-4). Il framework risiede in `vendor/omega-mvc/framework/` (namespace `Omega\`, sorgenti in `src/Omega/`); il codice applicativo in `app/` (namespace `App\`).
- Entrypoint: `public/index.php` (web) e script `omega` (CLI); entrambi caricano `bootstrap/app.php`, che restituisce il container `Application`.
- Config: `config/*.php` restituiscono array PHP con helper `env()`; `.env` viene caricato da `Env::load()` in `bootstrap/app.php`.

## Comandi essenziali
- Dev server: `php omega serve` (porta default 8000).
- Assets frontend (Vite + Tailwind): `npm install`, poi `npm run dev` oppure `npm run build`.
- Verifica completa: `composer run check` (= `lint` + `test`). Auto-fix: `composer run fix` (phpcbf, PSR-12 su `app/`).
- Test singolo: `vendor/bin/phpunit tests/Unit/BasicTest.php` (vedi gotcha sotto).
- Elenco completo dei comandi: `php omega list`.

## CLI e generazione
- Generatori: `make:controller`, `make:model`, `make:migration`, `make:view`, `make:middleware`, `make:provider`, `make:seeder`, `make:command`, `make:exception`.
- Database: `db:create` (solo se non esiste), `migrate` / `migrate:fresh|refresh|rollback|status`, `db:seed`.
- Cache: `config:cache`, `route:cache`, `view:cache` e relativi `*:clear`.

## Architettura rilevante
- Routes: `routes/web.php` + `routes/schedule.php` (cron). Sintassi placeholder stile `(:any)` (es. `/say/(:any)`); route via attributi registrate con `Router::register([Classe::class])`.
- Migrations in `database/migrations/` (al plurale).
- Models in `app/Models/`: estendono `Omega\Database\Model\Model` con proprietà `$tableName` e `$primaryKey`.
- Views: file `*.template.php` in `resources/views/`, engine "templator" (`{% extend %}`, `{% section %}`, `{{ $var }}`); layout in `base/base.template.php`, pagine di errore in `pages/`.

## Gotchas verificati
- **Suite di test:** `composer run test` fallisce eseguendo l'intera suite perché `bootstrap/app.php` imposta la proprietà statica `Application::$app` senza resettarla tra un test e l'altro (errore `Application::$app must be null...`). Eseguire i file di test singolarmente: `vendor/bin/phpunit tests/Feature/BasicTest.php`. Config in `phpunit.xml.dist`: `executionOrder="depends,defects"`, `failOnRisky` e `failOnWarning` attivi.
- **README obsoleto:** alcuni esempi non rispecchiano la CLI reale (es. `php omega make:services` non esiste). Verificare sempre con `php omega list`.
- **File ignorati:** `composer.lock`, `package-lock.json` e `phpcs.xml` sono in `.gitignore`; le modifiche alle configurazioni vanno fatte nei `.dist` corrispondenti.
- Convenzioni codice: `declare(strict_types=1)`, PSR-12, limite 120 colonne (`phpcs.xml.dist`).
