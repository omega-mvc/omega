# Pest 5 -> PHPUnit 13 mapping

Derived from Omega's own conversion (`ec35c6a`) and a census of the three Pest suites
(`expect(` 5331, `it(` 2976, `test(` 481, `throws(` 126, `beforeEach(` 100, `afterEach(` 69,
`uses(` 31, `group(` 30, `skip(` 12, `only(` 9, `dataset(` 9, `pest(` 4, `retry(` 2, `describe(` 2).

PHPUnit 13 is **attribute-based**. Do not use `@test`/`@dataProvider` docblock annotations; use
`#[Test]`, `#[DataProvider]`, `#[Group]`, etc. The starter convention names methods `test` +
CamelCase of the Pest description.

## File / class shape

| Pest 5 | PHPUnit 13 |
| --- | --- |
| `it('does x', function (): void { ... });` / `test('does x', fn)` | `public function testDoesX(): void { ... }` (or `#[Test] public function doesX()`) |
| `describe('group', function () { it(...); });` | One class per group (fold together, or use `#[TestDox]`) |
| `beforeEach(fn)` | `protected function setUp(): void { parent::setUp(); ... }` |
| `afterEach(fn)` | `protected function tearDown(): void { ...; parent::tearDown(); }` |
| `beforeAll(fn)` | `public static function setUpBeforeClass(): void` |
| `afterAll(fn)` | `public static function tearDownAfterClass(): void` |
| `uses(X::class)` / `pest()->extend(X::class)->in(...)` | Declare a base `tests/TestCase.php extends X` and extend it from each test class |
| `$this->...` inside a Pest closure | Same `$this` in a class method (bound to the TestCase in Pest) |
| Global function declared in `Pest.php` | `public static`/instance method on the base TestCase or a trait; or a namespaced function in a file autoloaded via composer `files` |
| `expect()->extend('foo', fn)` | Add an assertion method on the base TestCase (or a trait); call `$this->assertFoo(...)` |
| `arch(...)` | No equivalent: rewrite as explicit reflection assertions or drop |

Test classes need a namespace: `namespace <Root>\<Area>;` (starter: `Tests\App`), and each file gains
`declare(strict_types=1);` (already common) plus the class.

## Datasets and data providers

| Pest 5 | PHPUnit 13 |
| --- | --- |
| `dataset('name', [...])` + `->with('name')` | `public static function name(): array` + `#[DataProvider('name')]` |
| `->with([...])` (inline rows) | `#[TestWith([...])]` per row, or a provider |
| `->with([...])->each(fn)` | Put the body in a single test method driven by the provider |
| `#[DataProvider]` rows | Provider must be `public static`, return `array`/`iterable`; named keys become test names |

Gotcha (from `ec35c6a`): PHP normalises numeric-string provider keys (`'400'`) to `int`, so document the
shape as `array<int, ...>` for phpstan. Use `#[DataProviderExternal]` / `#[TestWithJson]` if needed.

## Grouping, skipping, dependencies

| Pest 5 | PHPUnit 13 |
| --- | --- |
| `->group('stress')` / `group('stress', fn)` | `#[Group('stress')]` (class or method); run `--group stress`, skip `--exclude-group stress` |
| `->skip('why')` / `skip()` | `$this->markTestSkipped('why');` (top of test); for conditions use `#[RequiresPhp]`, `#[RequiresPhpExtension]`, `#[RequiresFunction]`, `#[RequiresEnvironmentVariable]` |
| `->todo()` | `$this->markTestIncomplete();` |
| `->only()` | Remove for CI; locally run `--filter` |
| `->throws(E::class)` | `$this->expectException(E::class);` before the call |
| `->depends('other')` | `#[Depends('otherTest')]` |
| `->retry(n)` | No per-test retry; re-run with `--order-by=defects` or loop manually |
| `->dump()` / `dd()` | Delete (debug only) |

Pest `describe` + `#[TestDox('...')]` is the closest way to keep human-readable nested names.

## Expectations -> assertions

Argument order flips: Pest `expect($actual)->toBe($expected)` becomes `assertSame($expected, $actual)`.

| Pest expectation | PHPUnit assertion |
| --- | --- |
| `->toBe($v)` | `assertSame($v, $actual)` |
| `->toEqual($v)` | `assertEquals($v, $actual)` |
| `->toEqualCanonicalizing($v)` | `assertEqualsCanonicalizing($v, $actual)` |
| `->toBeTrue()` / `->toBeFalse()` | `assertTrue` / `assertFalse` |
| `->toBeNull()` | `assertNull` |
| `->toBeEmpty()` | `assertEmpty` |
| `->toBeArray()` / `->toBeString()` / `->toBeInt()` | `assertIsArray` / `assertIsString` / `assertIsInt` |
| `->toBeCallable()` | `assertIsCallable` |
| `->toBeInstanceOf(C::class)` | `assertInstanceOf(C::class, $actual)` |
| `->toHaveCount($n)` | `assertCount($n, $actual)` |
| `->toContain($v)` | string: `assertStringContainsString($v, $actual)`; array: `assertContains($v, $actual)` |
| `->toHaveKey($k)` | `assertArrayHasKey($k, $actual)` |
| `->toHaveKeys([...])` | loop `assertArrayHasKey` |
| `->toMatchArray($subset)` | No direct API: assert per key, or filter then `assertEquals` |
| `->toStartWith($p)` / `->toEndWith($p)` | `assertStringStartsWith` / `assertStringEndsWith` |
| `->toMatch($regex)` | `assertMatchesRegularExpression` |
| `->toBeGreaterThan($v)` / `->toBeGreaterThanOrEqual($v)` | `assertGreaterThan` / `assertGreaterThanOrEqual` |
| `->toBeLessThan($v)` / `->toBeLessThanOrEqual($v)` | `assertLessThan` / `assertLessThanOrEqual` |
| `->toBeBetween($a, $b)` | `assertGreaterThanOrEqual($a, $x)` + `assertLessThanOrEqual($b, $x)` |
| `->toBeFile()` / `->toBeReadableFile()` | `assertFileExists` / `assertFileIsReadable` |
| `->toThrow(E::class)` | `$this->expectException(E::class);` then invoke (only `expectException*`, there is **no** `assertThrows`) |
| `->toArray()` | `assertSame($expected, $actual->toArray())` (or `assertIsArray`) |
| `->toSerialize($v)` | `assertSame($v, serialize($actual))` or an `unserialize` round-trip |
| `->json()` | `json_decode($actual, true)` then assert; or `assertJsonStringEqualsJsonString` |

### Chaining and modifiers

| Pest | PHPUnit |
| --- | --- |
| `->and($v)` | A following assertion block on `$v` |
| `->not->toBe($v)` | `assertNotSame($v, $actual)` |
| `->not->toBeTrue()` / `->not->toBeFalse()` | `assertFalse` / `assertTrue` |
| `->not->toBeNull()` | `assertNotNull` |
| `->not->toBeEmpty()` | `assertNotEmpty` |
| `->not->toBeInstanceOf(C::class)` | `assertNotInstanceOf` |
| `->not->toContain($v)` | string: `assertStringNotContainsString`; array: `assertNotContains` |
| `->each(fn)` | Loop the collection and run assertions per element |
| `->sequence(...)` | Expand into ordered assertion pairs |

## `Pest.php` migration

- `pest()->extend(TestCase::class)->in('Feature', 'Unit')` -> `tests/TestCase.php` base class that each
  converted class extends.
- `uses()->bootstrap('tests/bootstrap.php')` -> the `bootstrap` attribute in `phpunit.xml.dist`.
- `expect()->extend(...)` -> custom assertion methods on the base TestCase.
- Helper functions -> static TestCase/trait methods.

Census command used to find idioms (run inside a package):

```bash
rg -o --no-filename -e '\b(expect|test|it|describe|beforeEach|afterEach|beforeAll|afterAll|dataset|uses|pest|arch|throws|todo|skip|group|only|retry)\s*\(' tests | sort | uniq -c | sort -rn
rg -o --no-filename -e '->to[A-Za-z]+' tests | sort | uniq -c | sort -rn
```
