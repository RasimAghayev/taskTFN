# IMPROVEMENT ROADMAP — taskTFN

- **Audit date (UTC):** 2026-11-10
- **Baseline:** Phase 3 audit complete; Phase 2 deps inventory available; no production code changed in Phase 3.
- **Gates:** Phase 3 requires **Gate 10 approval** before any Phase 4+ execution.

## 1. Objectives
1. Establish reproducible, secure, testable Laravel API baseline.
2. Resolve manifest/lock/runtime drift and modernize dependencies in controlled steps.
3. Fix correctness/contract/security defects with evidence-backed verification.
4. Add containerized dev/CI workflows suitable for constrained CI budgets.

## 2. Phase 4–11 detailed plan

### Phase 4 — Hygiene & repository normalization
**Applicability:** Yes  
**Dependencies:** Gate 10 approval  
**Tasks**
- Normalize repo docs (README, docs/*) to match verified API behavior/decisions (including auth contract decision).
- Confirm .gitignore coverage; ensure no .idea/, .vscode/, build artifacts, or secrets are tracked (delete/untrack only if found with evidence).
- Remove or clearly mark stale scaffold references in docs/comments inventory (no functional refactors beyond hygiene).
- Add/align baseline metadata files only as required (no AI attribution).
**Acceptance criteria**
- Docs reflect agreed contracts; no IDE artifacts tracked; no secrets in templates.
- Working tree remains buildable-equivalent for current baseline (or deviations documented).

### Phase 5 — Containerization baseline
**Applicability:** Yes  
**Dependencies:** Phase 4 baseline  
**Tasks**
- Add Dockerfile (PHP runtime aligned to chosen target), docker-compose.yml (app + MySQL + optional supporting services), and minimal entrypoint/init scripts.
- Externalize config via env; keep .env.example placeholders only.
- Provide deterministic commands for install, migrate/seed, serve, and test in containers.
- Document ports/volumes/health expectations.
**Acceptance criteria**
- docker compose up -d (or documented equivalent) brings up required services (exit 0).
- App reachable/bootable in container; DB migrations runnable with documented command and exit codes.

### Phase 6 — Dependency & runtime upgrades
**Applicability:** Yes (critical)  
**Dependencies:** Phase 5 environment (preferred), Gate approvals for risky upgrades  
**Tasks**
- Resolve composer.json/composer.lock mismatch; choose supported Laravel/PHP target matrix and align all coupled packages (framework, sanctum, tinker, collision, phpunit, ignition, guzzle, pint/sail as needed).
- Regenerate lockfiles (composer.lock, add package-lock.json) in controlled environment; avoid minimum-stability: dev unless justified.
- Upgrade Node/Vite/plugin chain to compatible versions; verify asset build.
- Run dependency audits where possible (composer audit, 
pm audit) and record results.
- Review flagged licenses (e.g. GPL transitive packages) and document disposition.
**Acceptance criteria**
- composer install and 
pm ci/install succeed reproducibly (exit 0).
- Application boots on target runtime; audit outputs captured (or explicit unavailability with evidence).

### Phase 7 — Dead code, correctness, and refactor
**Applicability:** Yes  
**Dependencies:** Phase 6 aligned baseline  
**Tasks**
- Fix broken relations/includes: implement/remove Purchase relation target; correct eager-load names (productpurchase vs productpurchases) and exposed includes.
- Normalize request/validation/update flow: use form requests/validated data for updates, align body vs URI identifiers, correct image contract (upload or URL) end-to-end.
- Clean dead scaffolding/comments (unused policy stubs or implement/register), redundant controller branches, unused params.
- Harden factories/seeders for deterministic setup; add DB constraints/indexes as warranted by schema and queries.
**Acceptance criteria**
- No references to missing classes/method contracts; CRUD/filter/include flows behave per documented contract.
- Static checks (if introduced) and updated tests pass for touched areas.

### Phase 8 — Security hardening
**Applicability:** Yes  
**Dependencies:** Gate 10 auth-contract decision; Phase 7 contract cleanup  
**Tasks**
- Enforce authentication/authorization for catalogue endpoints as approved (Sanctum guards/abilities/policies) and align /api/user behavior.
- Tighten CORS to allowed origins/methods/headers per environment.
- Mitigate mass-assignment and input handling risks; validate file uploads/storage paths if applicable.
- Review rate limiting, error exposure, logging of sensitive data, and .env/secret handling.
**Acceptance criteria**
- Protected endpoints reject unauthenticated/unauthorized access as specified; CORS restricted; security tests/checklist evidence recorded.

### Phase 9 — Observability & operations
**Applicability:** Partial  
**Dependencies:** Phase 5/8  
**Tasks**
- Improve structured logging and exception reporting for API failures/validation/auth errors (without leaking secrets).
- Add/verify health/readiness endpoint(s) and container healthchecks where useful.
- Document operational runbooks (start/stop, migrate, logs, common failures).
**Acceptance criteria**
- Actionable logs for key failure paths; documented operational checks succeed in Docker.

### Phase 10 — CI/CD & quality gates
**Applicability:** Yes (budget-aware)  
**Dependencies:** Stable Phase 6–8 baseline  
**Tasks**
- Add GitHub Actions workflows (minimal, cached): setup PHP/Node, install deps, lint/static analysis (if configured), PHPUnit/tests, build assets.
- Gate PRs on required checks; keep matrix small to control CI minutes.
- Optionally add Dependabot/config updates in a follow-up approved change.
**Acceptance criteria**
- CI workflows run green on baseline branch/PR (exit 0 for required jobs) with published evidence.

### Phase 11 — Verification & release readiness
**Applicability:** Yes  
**Dependencies:** Prior phases complete as applicable  
**Tasks**
- Execute end-to-end verification in Docker: install, migrate/seed, tests, API smoke checks, frontend build (if in scope).
- Capture command outputs and exit codes; reconcile docs (ARCHITECTURE, REPOSITORY_AUDIT, TECHNICAL_DEBT, API/DB docs).
- Record residual risks, deferred items, and approval state.
**Acceptance criteria**
- Evidence-backed pass/fail report; Gate requirements satisfied; repo ready for approved merge/release or explicitly blocked with reasons.

## 3. Docker and CI configuration notes (target state)
- **Docker:** multi-service compose (php app + nginx/php-fpm serving strategy as chosen + mysql), bind/mount strategy for dev, isolated test DB, env-driven config, no committed secrets.
- **CI:** ctions/checkout, cached Composer/npm, pinned action versions, least-privilege permissions, separate jobs only when value exceeds cost.

## 4. Critical path
1. **Gate 10 approval + auth-contract decision**
2. **Phase 4 hygiene** (quick baseline)
3. **Phase 5 Docker** (reproducible execution)
4. **Phase 6 dependency/runtime alignment** (unblocks reliable fixes/tests)
5. **Phase 7 correctness/refactor + Phase 8 security** (core product quality)
6. **Phase 10 CI + Phase 11 verification** (sustained confidence)

## 5. Risks, assumptions, and out-of-scope
- **Risks:** large framework jump (locked 9.x vs manifest 12.x target), undocumented intended auth behavior, missing domain entities (Purchase/orders), unconfirmed vulnerabilities due to tooling/lock gaps.
- **Assumptions:** MySQL-backed deployment; catalogue API intended for authenticated clients unless Gate 10 states otherwise.
- **Out of scope for this phase:** production code changes, dependency upgrades, container/CI implementation, live vulnerability remediation.
