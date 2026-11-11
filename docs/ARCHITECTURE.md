# ARCHITECTURE — taskTFN

## 1. Overview
- **Project:** `taskTFN` (Laravel-based REST API)
- **Domain:** Product catalogue with hierarchical categories and products
- **Entry points:** HTTP API (`/api`, `/api/v1/*`), default web welcome route (`/`)
- **Persistence:** Relational DB via Eloquent (MySQL configured in `.env.example`)

## 2. Runtime & framework
| Concern | Implementation |
|---|---|
| Framework | Laravel (manifest `^12.0`, lock `v9.52.16` — inconsistent; see audit) |
| PHP | Manifest `^8.1` |
| HTTP kernel | `app/Http/Kernel.php` (global + `web`/`api` groups, throttle alias) |
| Routing | `routes/api.php`, `routes/web.php`, `routes/console.php`, `routes/channels.php` |
| Auth | `laravel/sanctum` dependency; `User` has `HasApiTokens`; only `GET /api/user` guarded by `auth:sanctum` |
| Config | Standard Laravel config under `config/*` (app, auth, cache, cors, database, hashing, sanctum, etc.) |

## 3. Module / directory structure (relevant)
```text
app/
  Console/Kernel.php
  Exceptions/Handler.php
  Filters/
    ApiFilter.php            # base query transform (operator map, safe params)
    CategoryFilters.php      # categoryName/categoryParentId whitelist + column map
    ProductFilters.php       # product fields whitelist + column map
  Http/
    Controllers/
      CategoryController.php # index/show/store/update/destroy + eager-load flags
      ProductController.php  # index/show/store/update/destroy + eager-load flags
    Middleware/              # standard Laravel middleware set
    Requests/
      ApiFormRequest.php     # authorize() => true
      CategoryRequest.php    # method-aware rules, camelCase->DB mapping
      ProductRequest.php     # method-aware rules, camelCase->DB mapping
    Resources/
      CategoryResource.php, CategoryCollection.php
      ProductResource.php, ProductCollection.php
  Models/
    Category.php             # fillable name,parent_id; relations to children/products
    Product.php              # fillable category/product fields; relations (incl. broken Purchase ref)
    User.php                 # Sanctum HasApiTokens
  Policies/CategoryPolicy.php (empty stubs, unregistered)
  Providers/                 # App/Auth/Broadcast/Event/Route providers
database/
  migrations/                # users, password_resets, failed_jobs, personal_access_tokens,
                              # categories, products
  factories/                 # CategoryFactory, ProductFactory, UserFactory
  seeders/                   # DatabaseSeeder -> CategorySeeder; ProductSeeder present
resources/                   # minimal JS/CSS/views (Vite entrypoints)
routes/api.php               # /api/user (sanctum), /api/v1 apiResources category,product
tests/                       # Example Feature/Unit tests only
docs/                        # api.md, database.md (+ Phase 3 audit docs)
```

## 4. Data model
### 4.1 Categories (`categories`)
- Columns: `id` (PK), `name` (string), `parent_id` (unsignedBigInteger, no FK constraint in migration), timestamps
- Eloquent: `Category::$fillable = [name,parent_id]`
- Relations:
  - `categoryparentids()` — `hasMany(Category::class,'parent_id','id')` (children)
  - `categoryproducts()` — `hasMany(Product::class,'category_id','id')`

### 4.2 Products (`products`)
- Columns: `id` (PK), `category_id` (unsignedBigInteger, no FK), `name`, `description` (longText), `price` (float 8,2), `image` (string), `status` (enum `0`,`1`, default `1`), timestamps
- Eloquent: `Product::$fillable = [category_id,name,description,price,image,status]`
- Relations:
  - `productcategories()` — `belongsTo(Category::class)`
  - `productpurchases()` — `hasMany(Purchase::class,'product_id','id')` (**`Purchase` model absent**)

### 4.3 Users / auth tables
- Standard Laravel `users`, `password_resets`, `failed_jobs`, Sanctum `personal_access_tokens` migrations present.

## 5. API contracts (implemented)
Base: `/api/v1` (apiResource registration; not behind `auth:sanctum` in current routes).

### Categories
| Method | Path | Handler | Notes |
|---|---|---|---|
| GET | `/api/v1/category` | `index` | Filters: `categoryName[eq|ne|lk]`, `categoryParentId[eq|ne]`; includes: `includeCategoryParentIds`, `includeCategoryProducts`; paginate(10), order by `parent_id asc` |
| POST | `/api/v1/category` | `store` | `CategoryRequest` validation; returns `CategoryResource` |
| GET | `/api/v1/category/{category}` | `show` | Optional eager loads; JSON envelope `{status,message,data}` |
| PUT/PATCH | `/api/v1/category/{category}` | `update` | Uses raw `Request`, `$category->update($request->all())`; returns status/message |
| DELETE | `/api/v1/category/{category}` | `destroy` | Returns id/status/message |

### Products
| Method | Path | Handler | Notes |
|---|---|---|---|
| GET | `/api/v1/product` | `index` | Filters via `ProductFilters`; includes: `includeProductOrders` (loads `productorders`), `includeProductPurchases` (loads `productpurchase`); paginate(10), order by `id desc` |
| POST | `/api/v1/product` | `store` | `ProductRequest` validation; returns `ProductResource` |
| GET | `/api/v1/product/{product}` | `show` | Loads `productorders`/`productpurchase` when requested; envelope response |
| PUT/PATCH | `/api/v1/product/{product}` | `update` | Raw `Request` mass update |
| DELETE | `/api/v1/product/{product}` | `destroy` | Returns id/status/message |

### Response shaping
- Resources map to camelCase: Category (`categoryId,categoryName,categoryParentId,categoryCreatedAt,categoryUpdatedAt,categoryParentIds,products`), Product (`productId,productCategoryId,productName,productDescription,productPrice,productImage,productStatus,productCreatedAt,productUpdatedAt`).
- Collections use default `ResourceCollection::toArray` (paginated wrapper from paginator).

## 6. Validation & filtering
- `ApiFilter::transform()` builds `[column, operator, value]` clauses from whitelisted params/operator map; wraps values with `%` for `like/not like`.
- Form requests: method-switched rules; `prepareForValidation()` remaps external names to DB columns and merges `id` when provided.
- **Notable gaps:** update path bypasses form requests; image field required as uploaded file in validation but treated as string elsewhere; PUT contract includes body IDs alongside route-model binding.

## 7. Configuration & cross-cutting concerns
- **CORS:** `paths` api/sanctum csrf, `allowed_* => *`, `supports_credentials=false` (`config/cors.php`).
- **Hashing:** bcrypt driver (`config/hashing.php`).
- **Rate limiting:** `RouteServiceProvider` defines `api` limiter 60/min per user/IP; applied to `api` middleware group.
- **Auth guard:** default web/session guard only; no `sanctum` guard configured in `config/auth.php`.
- **Queue/cache/session/mail:** default Laravel drivers per config/env (sync/file/log etc. in example).

## 8. Frontend/build assets
- Minimal Vite setup: `vite.config.js`, `resources/js/app.js`, `resources/js/bootstrap.js` (axios/lodash usage), `resources/css/app.css`, Blade welcome view.
- No `package-lock.json`; build pipeline not validated in this audit.

## 9. Testing & CI
- PHPUnit 9.5 XML (`phpunit.xml`) with Unit/Feature suites, array drivers for cache/mail/session/queue in test env.
- Tests: `tests/Unit/ExampleTest.php`, `tests/Feature/ExampleTest.php` (root 200) — no domain coverage.
- CI/CD: none detected.

## 10. Deployment/containerization
- No Dockerfile/docker-compose; no Makefile; deployment docs not established.
- `.env.example` provides placeholders; `APP_KEY` empty by default.

## 11. Architectural risks (summary)
- Manifest/lock/runtime misalignment blocks reproducible installs/upgrades.
- Auth/authorization not enforced on catalogue resources despite Sanctum presence and README claims.
- Broken/placeholder domain references (`Purchase` model, eager-load name mismatches) and permissive CORS/mass-update patterns.
