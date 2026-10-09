---
name: Gettext Compliance
description: Audit and fix the omega-mvc/gettext package so it complies with the project's quality gates (PSR-12, PHPStan level 10, 100% type coverage, tests/CI) and with the GNU gettext + Unicode CLDR specifications (PO/MO formats, plural forms, CLDR plural rules). Use when maintaining, reviewing or extending vendor/omega-mvc/gettext, or when asked to find and implement what is missing or wrong in that package.
---

# gettext package compliance

`vendor/omega-mvc/gettext` (`Omega\Gettext\`) is a standalone i18n/l10n library with its own git repo.
This skill audits it against **two authorities only** and then implements what is missing or fixes what is wrong:

1. **Project quality gates** — the package's own `phpcs`, `phpstan` (level 10), type-coverage and test/CI configuration.
2. **GNU gettext + Unicode CLDR** — PO/MO file formats, plural forms, CLDR plural rules.

**Do not use the upstream `php-gettext/Gettext` as a reference: it is abandoned and this package deliberately
diverges.** The README still cites it (with the old `Gettext\` namespace) — ignore that.

## Golden rules

- Read `vendor/omega-mvc/gettext/AGENTS.md` first; it has the exact commands and the intentional exceptions.
- Work from the package directory and run `vendor/bin/*` **directly** (`composer*` is denied by the project
  permissions). Prefix `XDEBUG_MODE=off` for phpcs/phpstan.
- **Never restyle or analyse**: `src/Omega/Gettext/Languages/**` (vendored gettext/languages),
  `tests/Tests/Gettext/assets/**`, `tests/data.php`, `tests/data.json`. Keep every documented exclusion intact.
- Fix production code only for a real spec/quality defect, and add a regression test for every fix.
- This is a separate git repo: commit there; the starter app does not track `vendor/`.
- **Report before you rewrite.** Produce a findings list (missing / wrong / untested) with evidence before
  editing, so the user can prioritise. Only start editing when asked to fix (or after the user picks items).

## Workflow

1. **Baseline.** From the package directory run and record:
   ```bash
   XDEBUG_MODE=off php vendor/bin/phpcs
   XDEBUG_MODE=off php vendor/bin/phpstan analyse
   vendor/bin/pest --type-coverage
   XDEBUG_MODE=off php vendor/bin/pest --no-coverage
   ```
2. **Quality audit.** Compare each result with `references/quality-gates.md`: gate failures, missing native types,
   missing tests, CI mismatch, broken exclusions.
3. **Domain audit.** Walk `references/gnu-gettext-spec.md` and `references/cldr-plural-spec.md` area by area
   (loaders, generators, scanners, translator, headers, languages) and record every gap against the spec clause.
4. **Findings report.** Output a prioritised list: what is missing, what is implemented incorrectly, what is
   untested — each with `file:line`, the exact spec clause, and a suggested fix.
5. **Implement.** Apply the smallest correct change, add regression tests, re-run every gate touched.
6. **Verify and commit.** All gates green, commit inside the package repo in the existing message style.

## Never assume — verify first

The references list concrete **candidate** gaps (e.g. `MoGenerator` endianness, `MoLoader` `array_filter`,
`FormulaConverter` parenthesis handling). They are investigation leads, not confirmed bugs: reproduce each with a
test or a minimal script before changing code, and say so in the report.

## References

- `references/quality-gates.md` — exact gates, thresholds, exclusions and CI.
- `references/gnu-gettext-spec.md` — PO/MO/plural-forms checklist plus candidate gaps.
- `references/cldr-plural-spec.md` — CLDR TR35 variables/categories/conversion and the languages data.
