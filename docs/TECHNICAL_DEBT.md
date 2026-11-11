# TECHNICAL DEBT — taskTFN

- **Audit date (UTC):** 2026-11-11
- **Phase:** 3 (documentation only)
- **Sources:** `projects/info/analysis/taskTFN.md`, `projects/info/deps/taskTFN.md`, in-repo audit docs

## 1. Debt register

### P0 — Critical (correctness / security / installability)
| ID | Area | Location | Debt item | Impact | Recommended remediation | Target phase |
|---|---|---|---|---|---|---|
| TD-P0-1 | Dependency/install | `composer.json`, `composer.lock` | Manifest requires `laravel/framework ^12.0`, `nunomaduro/collision ^8.0`, `php ^8.1` but lock resolves framework 9.52.16/collision 6.x; lock stale/inconsistent | Non-reproducible/uncertain install; blocked upgrades and CI reliability | Align PHP/runtime + framework target, regenerate lock in controlled environment, verify app boot/tests | 6 |
| TD-P0-2 | API security/contract | `routes/api.php` | `/api/v1/category` and `/api/v1/product` resources not protected by `auth:sanctum` while README/docs imply protected API | Unauthorized access or contract ambiguity | Decide intended access model; enforce auth/abilities and update docs/tests accordingly | 8 (decision at Gate 10) |
| TD-P0-3 | Domain correctness | `app/Models/Product.php` | `productpurchases()` references missing `App\Models\Purchase` | Fatal/error on relation load/use; broken eager-load paths | Implement missing model/migration or remove/fix relation and includes consistently | 7 |

### P1 — High (quality, validation, maintainability risk)
| ID | Area | Location | Debt item | Impact | Recommended remediation | Target phase |
|---|---|---|---|---|---|---|
| TD-P1-1 | Validation/mass assignment | `CategoryController@update`, `ProductController@update` | Untyped `Request` + broad mass update bypasses form-request validation/mapping | Data integrity and security risk | Use dedicated update requests, validated DTO/input, explicit fillable mapping | 7/8 |
| TD-P1-2 | API contract | `CategoryRequest`, `ProductRequest`, controllers | Body ID requirements/mapping conflict with route-model binding; method-aware rules incomplete for REST semantics | Client confusion, brittle updates/deletes | Normalize contract (URI id authoritative), align rules and payload schema | 7 |
| TD-P1-3 | Media handling | `ProductRequest`, `ProductController@store` | `productImage` validated as uploaded file but treated/stored as string URL; no upload/storage flow | Broken create/update for documented payloads or silent inconsistency | Define canonical image contract (URL vs upload), implement storage/validation accordingly | 7 |
| TD-P1-4 | Security posture | `config/cors.php` | Wildcard origins/methods/headers | Overly permissive cross-origin access | Restrict to known origins per environment | 8 |
| TD-P1-5 | Test coverage | `tests/*` | Only example tests; no endpoint/domain/validation/auth coverage | Regressions undetected, unsafe upgrades | Add feature/unit tests for resources, filters, validation, auth decisions | 6/10/11 |
| TD-P1-6 | Frontend reproducibility | root `package.json` | No `package-lock.json`; `npm audit` ENOLOCK | Unpinned tree, unknown vulnerabilities | Generate lockfile, pin toolchain, run audit/build checks | 6/10 |
| TD-P1-7 | Eager-load correctness | `ProductController@index` | Loads `productpurchase` (singular) vs relation `productpurchases()` | Requested include may not work | Fix relation names/includes and cover with tests | 7 |

### P2 — Medium (hygiene, design gaps, docs drift)
| ID | Area | Location | Debt item | Impact | Recommended remediation | Target phase |
|---|---|---|---|---|---|---|
| TD-P2-1 | Authorization scaffolding | `CategoryPolicy`, `AuthServiceProvider` | Empty policy, not registered | Misleading structure, no enforcement | Remove or implement/register policies | 7/8 |
| TD-P2-2 | Data/schema rigor | migrations, factories | No FK constraints/index strategy shown; factories assume existing/random records | Data integrity/perf and flaky seeds | Add constraints/indexes as appropriate, harden factories/seed order | 7 |
| TD-P2-3 | Filtering/validation depth | `ApiFilter`, filter classes | Limited normalization and error feedback for invalid operators/values | Ambiguous API behavior | Tighten validation/normalization and document supported operators | 7/8 |
| TD-P2-4 | Documentation drift | `README.md`, `docs/api.md` vs code | Auth/operators/params not fully aligned with implementation | Integrator confusion | Update docs from verified behavior after fixes | 4/11 |
| TD-P2-5 | CI/CD & automation | repo root | No pipelines, no Dependabot, no quality gates | Drift and regressions | Add minimal CI, cache-aware jobs, dependency automation as approved | 10 |
| TD-P2-6 | Containerization | repo root | No Docker dev/runtime baseline | Hard to verify consistently | Add Docker/compose baseline for app + db | 5 |
| TD-P2-7 | License/compliance review | transitive deps (Phase 2) | GPL packages `nette/schema`, `nette/utils` flagged | Compliance review needed | Legal/compliance review during upgrade planning | 6/11 |

### P3 — Low (cleanup, polish)
| ID | Area | Location | Debt item | Impact | Recommended remediation | Target phase |
|---|---|---|---|---|---|---|
| TD-P3-1 | Dead/commented code | `ProductResource` | Commented-out relation includes | Noise | Remove or implement with tests | 7 |
| TD-P3-2 | Controller flow | `CategoryController@show`, `ProductController@show` | Redundant existence checks after route-model binding | Minor confusion | Simplify control flow | 7 |
| TD-P3-3 | Unused default surface | `routes/web.php`, welcome view | Default Laravel landing retained | Minor footprint | Keep or replace intentionally; document | 4 |

## 2. Dependency debt highlights (from Phase 2 inventory)
- Major runtime/package gaps: Laravel lock 9.52.16 vs manifest ^12 / latest 13.x, Sanctum 3→4, Tinker 2→3, Guzzle 7→8, PHPUnit 9→13, spatie/laravel-ignition 1→2, collision lock/manifest mismatch, Vite 3→8, laravel-vite-plugin 0.8→3.
- Toolchain/status: `minimum-stability: dev`; PHP ^8.1 manifest vs Laravel 12 requirement (^8.2); no `package-lock`.
- Unknowns: vulnerabilities and unused dependencies **unconfirmed** (no `composer audit`; `npm audit` blocked).

## 3. Remediation roadmap mapping (burndown targets)
| Phase | Debt focus | Exit target |
|---|---|---|
| 4 | Hygiene/docs normalization (TD-P2-4 partial, TD-P3-3) | Repo/docs consistent, no tracked IDE junk, clear baseline |
| 5 | Container baseline (TD-P2-6) | Reproducible Docker environment for app + DB |
| 6 | Dependency/runtime alignment (TD-P0-1, TD-P1-5/6 partial, TD-P2-7 review) | Consistent manifest/lock/runtime, installable deps, lockfiles generated |
| 7 | Refactor/correctness (TD-P0-3, TD-P1-1/2/3/7, TD-P2-1/2/3, TD-P3-1/2) | Correct relations/contracts/validation behavior with tests for changes |
| 8 | Security hardening (TD-P0-2, TD-P1-4, related validation/auth) | Agreed auth model enforced, CORS/input/mass-assignment tightened |
| 9 | Observability/ops (supporting) | Actionable logs/errors/health signals as needed |
| 10 | CI/CD (TD-P1-5/6, TD-P2-5) | Automated lint/test/build gates, minimal budget-aware workflows |
| 11 | Verification/readiness | Evidence-backed test/build/run results; release-ready state or explicit residual risks |

## 4. Burndown summary
- **Must resolve before reliable upgrade/CI:** TD-P0-1, TD-P0-3, TD-P1-1, TD-P1-2, TD-P1-6.
- **Must decide before implementation:** TD-P0-2 (auth contract) — Gate 10 decision.
- **Can defer with documented risk:** P3 items and selected P2 docs/automation tasks until corresponding phases.
