# CLDR plural rules (TR35) — audit checklist

Authority: Unicode CLDR TR35, Language Plural Rules
(<https://unicode.org/reports/tr35/tr35-numbers.html#Language_Plural_Rules>). The package generates gettext
`Plural-Forms` from this data, so the conversion must stay faithful.

## Categories and operands

- Cardinal categories: `zero`, `one`, `two`, `few`, `many`, `other`.
- Operands: `n` (absolute value), `i` (integer digits), `v` (visible fraction digits incl. trailing zeros),
  `w` (fraction digits without trailing zeros), `f`, `t`, `c`, `e`.

## CLDR → gettext conversion

- gettext only exposes `n` as an integer. Mapping used by the package:
  `i → n`; `v, w, f, t, c, e → 0` (empty).
- Generated gettext formulas add **explicit parentheses** because PHP's ternary is right-associative:
  CLDR `a ? 0 : b ? 1 : 2` must become `a ? 0 : (b ? 1 : 2)`.
- The gettext expression must be a C boolean over integer `n` returning an index in `[0, nplurals-1]`.

## Package pieces

- `Languages/CldrData` — CLDR data holder: `$categories = ['zero','one','two','few','many','other']`,
  `getLanguageNames/getTerritoryNames/getScriptNames(bool standAlone)/getPlurals/getSupersededLanguages/`
  `getLanguageInfo()`, plus data normalisation (drops root/und/zxx/ZZ, handles `-alt-` variants, drops
  languages without plurals, patches `jw→jv`, `mo→ro_MD` and known missing languages).
- `Languages/cldr-data/main/en-US/{languages,scripts,territories}.json` + `supplemental/plurals.json`.
- `Languages/FormulaConverter::convertFormula()` — CLDR formula → gettext. **Candidate gap:** it throws
  `parenthesis handling not implemented` for any input containing `()` and only handles ` or `, ` and `, a
  few range forms and one hard-coded simplification. Check CLDR formulas that use parentheses/ranges and
  either implement the missing forms or document the limitation precisely.
- `Languages/Language::getAll()` / `getById()`; `Languages/Category`.
- Exporters `Languages/Exporter/{Exporter,Html,Json,Php,Po,Prettyjson,Ruby,Xml}` and
  `bin/export-plural-rules` (options `--us-ascii`, `--languages`/`--language`, `--reduce`, `--parenthesis`,
  `--output`); `bin/import-cldr-data` regenerates the CLDR JSON.

## Verify

- Formula round-trip: CLDR formula → `FormulaConverter` → evaluate for sample numbers and compare the chosen
  category with the CLDR examples (`pluralRule-count-*` / `Language` categories).
- `Headers::setPluralForm()` / `getPluralForm()` round-trip, and `Translator` parsing/compiling the
  `Plural-Forms` header (it validates the compiled closure returns `int|bool`).
- `bin/export-plural-rules` output for every format still matches its snapshots under
  `tests/Tests/Gettext/Languages/` (snapshots) — regenerate only via the tool, never by hand.
- New/changed CLDR data goes through `bin/import-cldr-data`; never hand-edit `cldr-data/**`.
