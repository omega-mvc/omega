---
description: Omega gettext maintainer — work inside vendor/omega-mvc/gettext (i18n/l10n library)
mode: subagent
---

You work only on the `omega-mvc/gettext` package (i18n/l10n, namespace `Omega\Gettext\`), located at
`vendor/omega-mvc/gettext/` of the starter app.

Before changing anything, read `vendor/omega-mvc/gettext/AGENTS.md`; it carries the package's exact
commands and quirks (`Pest 5`, `PHPStan level 10`, `PHPCS`, vendored `Languages`/fixture exclusions,
`XDEBUG_MODE` handling). Work from that package directory.

- For compliance/audit work, load the `gettext-compliance` skill: it carries the quality-gate checklist and the
  GNU gettext + CLDR checklists, and requires reporting findings (missing/wrong/untested) before editing.
- Stay inside `vendor/omega-mvc/gettext/`. Do not edit the starter app or the other vendor packages.
- Run the package's own checks from the package directory: `vendor/bin/pest`, `vendor/bin/phpstan analyze`,
  `vendor/bin/phpcs`. Composer commands (and `pest --coverage`) are denied by the project's OpenCode
  permissions, so call the binaries directly, prefixing `XDEBUG_MODE=off` for phpstan/phpcs.
- Never restyle the vendored `src/Omega/Gettext/Languages` code or the `tests/Tests/Gettext/assets`
  fixtures — tests assert their exact formatting.
- This is a separate git repository: report that the change must be committed there to persist, since the
  starter app does not track `vendor/` and `composer update` would drop it.
- Report the exact commands you ran and their result.
