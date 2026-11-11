# REPOSITORY AUDIT — taskTFN

- **Audit date (UTC):** 2026-11-11
- **Scope:** Phase 3 full read-only audit (code/config/docs/tests/deps/git hygiene). No production code modified.
- **Baseline:** canonical repo projects/repos/taskTFN, worktree projects/worktrees/taskTFN @ 32887e8 (docs-only audit commit on detached HEAD; canonical master `454dcc6`)

## 1. Inventory
| Area | Present | Notes |
|---|---|---|
| Application code | Yes | `app/` (Controllers, Filters, Requests, Resources, Models, Policies, Providers, Middleware) |
| Routes | Yes | `routes/api.php`, `web.php`, `console.php`, `channels.php` |
| Config | Yes | Full `config/*.php` set (app, auth, broadcasting, cache, cors, database, filesystems, hashing, logging, mail, queue, sanctum, services, session, view) |
| Database | Yes | 6 migrations, 3 factories, 3 seeders |
| Tests | Minimal | 2 example tests + TestCase/CreatesApplication; no domain tests |
| Docs | Yes | README.md, docs/api.md, docs/database.md (pre-existing) |
| CI/CD | No | No `.github/workflows`, `.gitlab-ci.yml` |
| Containers | No | No `Dockerfile*`, `docker-compose*` |
| IDE artifacts tracked | No evidence in worktree | `.idea/`, `.vscode/` absent; `.gitignore` already ignores them |
| Lockfiles | Partial | `composer.lock` present (stale vs manifest); `package-lock.json` missing |

## 2. Dependency analysis (evidence)
| Item | Command / source | Exit | Finding |
|---|---|---|---|
| Direct composer outdated | `composer outdated --direct` | n/a | `composer` not installed on host (CommandNotFoundException) |
| npm audit | `npm audit` | 1 | ENOLOCK — requires existing `package-lock.json` |
| Dependency inventory | `projects/info/deps/taskTFN.md` (Phase 2, 2026-11-10) | — | Live Packagist/npm checks recorded: major gaps (framework/sanctum/tinker/guzzle/phpunit/ignition/collision/vite/plugin), lock-manifest mismatch, `minimum-stability: dev`, PHP/runtime inconsistency, GPL transitive licenses (`nette/schema`, `nette/utils`), vulns/unused unconfirmed |

## 3. Configuration review
| Config | Observation | Risk |
|---|---|---|
| `.env.example` | Placeholders/empty values only (`APP_KEY=`, DB creds empty/local defaults) | OK for template; secrets must remain uncommitted |
| `config/app.php` | Standard providers/aliases, debug from env, key from env | Low |
| `config/auth.php` | Default web session guard/provider only; no API/sanctum guard | Aligns poorly with token-protected API intent |
| `config/cors.php` | `allowed_origins`/methods/headers = `*`, credentials false | Permissive; review for production |
| `config/hashing.php` | bcrypt driver | OK |
| `config/sanctum.php` | Default stateful domains/guard/middleware | OK if SPA use intended; API guard wiring separate |
| `phpunit.xml` | PHPUnit 9.5 schema, array drivers for tests | Needs update with PHPUnit/runtime upgrades |
| `.gitignore` | Ignores vendor/node_modules/env/build/cache/IDE dirs | Adequate baseline |

## 4. Code quality findings
| ID | Location | Finding |
|---|---|---|
| CQ-1 | `CategoryController`, `ProductController` `update()` | Accept raw `Request`, call `update` with broad request input; bypass typed validation/mapping used on store. |
| CQ-2 | `ProductController@index` | Eager-loads `with('productpurchase')` but relation is `productpurchases()` (name mismatch). |
| CQ-3 | `Product` model | Declares `productpurchases()` to missing `App\Models\Purchase`. |
| CQ-4 | `CategoryController@show` / `ProductController@show` | Dead conditional branch after route-model binding; inconsistent 404 behavior. |
| CQ-5 | `ProductRequest` | Requires `productImage` as image file while domain/examples treat image as URL/string; store path does not persist uploaded file. |
| CQ-6 | `CategoryRequest`/`ProductRequest` `prepareForValidation` | Merges `id` and remapped attributes into request; coupled to body-id contract diverging from route binding. |
| CQ-7 | `ApiFilter` | Minimal operator/value normalization; relies on whitelist but limited hardening. |
| CQ-8 | Factories | `CategoryFactory`/`ProductFactory` use `inRandomOrder()->first()` assumptions that can break on empty datasets/order. |
| CQ-9 | `CategoryPolicy` | Stub-only, not registered; no effective authorization rules. |
| CQ-10 | Resources | `ProductResource` has commented relation fields; minor dead/commented code. |

## 5. Security scan
| Check | Result | Evidence/notes |
|---|---|---|
| Secret values in repo config | No obvious committed secrets | `.env.example` contains placeholders/empty values only; `.gitignore` ignores `.env*` and keys (`<REDACTED>` policy applied) |
| Git history spot-check | Not exhaustive | `git log --oneline -10` reviewed; full history secret scan recommended in Phase 8 |
| Auth enforcement | Gap | `/api/v1/category` and `/api/v1/product` not behind `auth:sanctum` (`routes/api.php`) |
| Mass assignment exposure | Risk | Broad request update pattern; models define `$fillable` but controllers accept broad input |
| CORS posture | Permissive | wildcard origins/methods/headers |
| Input/file handling | Mismatch/risk | image validation vs storage contract unclear |
| Dependency vulnerabilities | Unconfirmed | no `composer audit` / `npm audit` with lockfile |
| Hashing/auth primitives | Standard defaults | bcrypt; Sanctum package present but partial wiring |

## 6. CI/CD status
- **Pipelines:** none.
- **Quality gates:** none enforced (no lint/static analysis/tests/coverage workflows detected).
- **Automation:** no Dependabot config observed.
- **Implication:** regressions and dependency drift undetected; establish baseline in Phase 10 after stable tests/runtime.

## 7. Git hygiene
| Item | Observation |
|---|---|
| Remotes | `origin` points to local backup mirror `D:\...\projects\info\backups\taskTFN.git` (fetch/push) |
| HEAD | `32887e8 chore: commit pending audit documentation` (detached worktree); canonical master `454dcc6` |
| Recent history | Renovate/dependabot/merge commits visible; manifest/lock drift suggests incomplete upgrade application |
| Tracked IDE dirs | None found in worktree snapshot |
| Working tree changes (this audit) | Docs-only additions under `docs/` in worktree (Phase 3 artifacts); no production code edits intended |

## 8. Test inventory
| Suite | Files | Tests (static) | Notes |
|---|---|---|---|
| Unit | `tests/Unit/ExampleTest.php` | 1 | `assertTrue(true)` |
| Feature | `tests/Feature/ExampleTest.php` | 1 | `GET /` expects 200 |
| Domain/API | — | 0 | No Category/Product/auth/validation tests |
| Config | `phpunit.xml` | — | Coverage includes `app/`; sqlite in-memory option commented |

**Execution status:** not run in this phase (no guaranteed PHP/vendor environment in host; Docker target not yet provisioned). Exit codes not produced for PHPUnit.

## 9. Summary assessment
- **Maturity:** early/scaffolded API with partial implementation and documentation; significant correctness/contract and dependency-alignment debt.
- **Primary blockers:** manifest-lock-framework mismatch, missing auth enforcement vs stated contract, broken/mismatched relations and request/update handling.
- **Recommended next step:** Gate 10 approval, then proceed Phase 4+ per roadmap (hygiene → containerization → controlled upgrades → refactor/security/tests/CI).
