# Security & serialization correctness — property checklist

Authority: the explicit properties below. Laravel is a feature-comparison reference only, never the oracle.
A serialized closure is **untrusted input** once it has crossed a trust boundary (cache, queue, session,
DB, request) except when signed with a secret only the application knows.

## 1. Threat model & guarantees

- Deserializing a crafted payload must **not** achieve remote code execution or file/stream inclusion.
- Signed mode must guarantee **integrity + authenticity** over the whole serialized closure.
- Signing must be **fail-closed**: a missing/empty key throws, it never silently downgrades to unsigned.
- Round-trip fidelity: a closure serialized then unserialized must behave like the original (body, `use`
  vars incl. by-ref, bound object, scope, static-ness, attributes, first-class callables).

## 2. Signing — `Signers/Hmac`, `Serializers/Signed`, `SerializableClosure::setSecretKey`

- [ ] MAC is HMAC-SHA256 over the serialized payload; comparison is timing-safe (`hash_equals`). ✔ present.
- [ ] No signer → `__serialize`/`__unserialize` throw `MissingSecretKeyException` (fail-closed).
- [ ] Verification runs **before** `unserialize()`; a bad hash throws `InvalidSignatureException`.
- [ ] After verification the payload must be a `SerializableInterface`, else reject.
- [ ] Secret must be non-empty: `setSecretKey()` uses a **truthy** check, so `''` and `'0'` silently select
      unsigned `Native` — **candidate HIGH**: decide whether empty keys must be rejected (throw) and cover with a test.
- [ ] `Signed::$signer` is global mutable static state: confirm `setSecretKey`/`flush` fully reset it to avoid
      cross-test and cross-request leakage.

## 3. Deserialization / RCE guard — `Serializers/Native`

- [ ] `assertRestorableClosureSource()` parses the payload as an expression and `isSingleClosureLiteral()`
      rejects statement separators/trailing statements, so `fn()=>1; system(...)` cannot escape. Enumerate
      comment, newline, `?>`(close-tag), heredoc/nowdoc and nested-closure evasions; each must be rejected.
- [ ] The `include` target is only ever `ClosureStream::STREAM_PROTO . '://' . <code>` (no attacker path),
      and the included result must be a `Closure`, else `ReflectionException`.
- [ ] `rebuildFromUse()` runs `extract($use, EXTR_OVERWRITE|EXTR_REFS)` inside an isolated method so hostile
      `use` keys cannot shadow `$function/$scope/$bound/$deferredObjects`. Do not move it into `__unserialize`.
- [ ] **Candidate:** `assertRestorableClosureSource()` tokenizes with `TOKEN_PARSE` then `isSingleClosureLiteral()`
      re-tokenizes *without* it — confirm the two passes cannot disagree on a rejected/accepted payload.

## 4. Stream — `Support/ClosureStream`

- [ ] Protocol `omega-serializable-closure`; content is `"<?php\nreturn " . substr($path, strlen(proto.'://')) . ';'`.
- [ ] Registration is idempotent (`stream_wrapper_register` once).
- [ ] The code reaches the stream only from the guard-validated payload; no path traversal is possible.

## 5. Reflection / round-trip — `Support/ReflectionClosure`

- [ ] `getCode()` preserves every closure form: `function`/`fn`, `static`, arrow fns, nested closures,
      `use` by value and by reference, first-class callables (`strlen(...)`), attributes (`#[...]`),
      namespace-qualified names, `::class`, magic constants, `#trackme` injection. Fixtures: `TokenizerEdgeCases`,
      `Grouped/*`, `Rich/*`, `MethodHost`, `TraitProbe`.
- [ ] Attribute reconstruction only allows scalar args (throws on non-scalar) — intended, keep.
- [ ] `getFileTokens()` throws when the source file is missing.
- [ ] **Candidate:** static caches `$files/$classes/$functions/$constants/$structures` keyed by `sha1(fileName)`
      are never reset — assess long-process memory growth and whether reuse across tests leaks state.

## 6. Binding, scope & pointers

- [ ] `bindTo($bound, $scope)` with the captured `this`/scope; `SelfReference` resolves back-references.
- [ ] `mapPointers`/`mapByReference` handle nested arrays, `stdClass`, user objects, shared-closure dedup
      (`SplObjectStorage`), and pass through `DateTimeInterface`/`UnitEnum`/non-user-defined types.
- [ ] Read-only properties are skipped when rebinding; user-object cloning uses
      `newInstanceWithoutConstructor` and `wrapClosures` recurses with `ARRAY_RECURSIVE_KEY`.

## 7. Errors & API stability

- [ ] `InvalidSignatureException` / `MissingSecretKeyException` carry the intended default messages and are
      thrown (never swallowed) on tamper/absent-key.
- [ ] **Candidate:** `Serializers/SerializableInterface::__invoke(): mixed` is declared **parameterless** while
      both implementations declare `__invoke(mixed ...$args): mixed` — verify the interface contract and callers.
- [ ] Public API and the on-the-wire serialized shape are consumed by the framework: flag any format/API change
      as a downstream-impact item before doing it.

## 8. Known PHP limits — document, do not "fix"

- By-reference closure parameters and anonymous-class captures cannot be serialized (inherent, also in Laravel).
- `Native::$transformUseVariables`/`resolveUseVariables` hooks are user-supplied; keep them out of the trusted path.
