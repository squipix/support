# Security Audit Report

- Date: 2026-03-09
- Project: squipix/support
- Scope: `src/`, `helpers.php`, selected `tests/` behavior checks, Composer advisory audit

## Executive Summary

No critical remote-code-execution or SQL-injection primitives were found in the current library code.

Two actionable security issues were identified:

1. Global mass-assignment unguard may remain enabled if a seeder throws.
2. `Stub` allows arbitrary file read/write paths if caller input is not trusted.

Composer dependency advisory audit result:

- `composer audit --format=json`: no known advisories, no abandoned packages.

## Findings

### 1) Seeder global guard state is not restored on exception

- Severity: Medium
- Location: `src/Database/Seeder.php:37` to `src/Database/Seeder.php:45`
- CWE: CWE-693 (Protection Mechanism Failure)

#### Details

`run()` calls `Eloquent::unguard()` before iterating seed classes, then `Eloquent::reguard()` after the loop. If `$this->call($seed)` throws, execution exits before `reguard()` runs.

This can leave mass-assignment protection disabled for the rest of the process, increasing risk of unintended attribute assignment in follow-up operations.

#### Recommendation

Wrap the seeding loop in `try/finally` to guarantee `Eloquent::reguard()` always runs.

#### Suggested patch

```php
public function run(): void
{
		Eloquent::unguard();

		try {
				foreach ($this->seeds as $seed) {
						$this->call($seed);
				}
		} finally {
				Eloquent::reguard();
		}
}
```

---

### 2) Stub read/write paths are fully caller-controlled

- Severity: Medium (context-dependent)
- Locations:
	- `src/Stub.php:211` to `src/Stub.php:214` (`saveTo`)
	- `src/Stub.php:221` to `src/Stub.php:224` (`getContents`)
- CWE: CWE-22 (Path Traversal), CWE-73 (External Control of File Name or Path)

#### Details

`saveTo($path, $filename)` performs `file_put_contents($path.DIRECTORY_SEPARATOR.$filename, ...)` with no canonicalization, allowlist, or traversal checks.

`getContents()` reads from `file_get_contents($this->getPath())`; `setPath()` accepts arbitrary paths and `getPath()` can include caller-controlled base paths.

If any consumer passes untrusted input into these APIs, this can enable arbitrary file read/write (for example `../../.env`-style traversal), depending on process permissions.

#### Recommendation

- Treat these APIs as trusted-input only, and document that requirement explicitly.
- If untrusted input is possible, enforce:
	- Canonical path resolution via `realpath`.
	- Base-directory confinement checks.
	- Filename validation/allowlist.
	- Optional extension restrictions for write targets.

---

## Hardening Notes (Low-Risk Trust Boundaries)

These are not direct vulnerabilities in this library by themselves, but they become dangerous if wired to untrusted configuration/data.

1. Dynamic container method invocation in route-class registration:
	 - `src/Routing/Concerns/RegistersRouteClasses.php:28`
	 - `src/Routing/Concerns/RegistersRouteClasses.php:42`
	 - Uses `app()->call("{$route}@...")`; ensure `$routes` list is trusted class references only.

2. Dynamic policy method binding strings:
	 - `src/Providers/AuthorizationServiceProvider.php:31`
	 - Uses `Gate::define($ability, "$class@$method")`; keep `$class/$method` sourced from static, trusted code.

## Positive Observations

- No use of `eval`, `exec`, `shell_exec`, `system`, `proc_open`, `popen`, or `unserialize` found in `src/`.
- No raw SQL helper usage (`DB::raw`, `whereRaw`, etc.) found in `src/`.
- Middleware and provider abstractions are generally minimal and framework-aligned.

## Residual Risk

Overall residual risk is Low-to-Medium and mainly depends on how downstream packages/apps pass data into this support library. Applying the seeder `try/finally` fix and documenting `Stub` trust requirements would significantly reduce risk.
