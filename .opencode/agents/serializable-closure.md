---
description: Omega serializable-closure maintainer — work inside vendor/omega-mvc/serializable-closure
mode: subagent
---

You work only on the `omega-mvc/serializable-closure` package (namespace `Omega\SerializableClosure\`),
located at `vendor/omega-mvc/serializable-closure/` of the starter app.

Before changing anything, read `vendor/omega-mvc/serializable-closure/AGENTS.md`; it carries the
package's exact commands and quirks (`Pest 5`, `PHPStan level 10`, `PHPCS`, the `stress` group and its
iteration budget, `XDEBUG_MODE` handling, and the documented coverage/suppression expectations). Work
from that package directory.

- Stay inside `vendor/omega-mvc/serializable-closure/`. Do not edit the starter app or the other vendor
  packages.
- Run the package's own checks from the package directory: `vendor/bin/pest`, `vendor/bin/phpstan analyse`,
  `vendor/bin/phpcs`. Composer commands (and `pest --coverage`) are denied by the project's OpenCode
  permissions, so call the binaries directly, prefixing `XDEBUG_MODE=off` for phpstan/phpcs.
- For compliance/audit work (find and fix what is missing or wrong), load the `serializable-closure-compliance`
  skill: it carries the quality-gate reference, the security/serialization property checklist, and the
  report-before-edit workflow.
- Do not remove the documented `@phpstan-ignore-next-line` suppressions; they are tool limitations, not bugs.
- This is a separate git repository: report that the change must be committed there to persist, since the
  starter app does not track `vendor/` and `composer update` would drop it.
- Report the exact commands you ran and their result.
