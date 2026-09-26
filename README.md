# taskTFN

A Laravel 9 REST API for managing a product catalogue: **categories** (hierarchical,
self-referencing) and **products** (owned by a category, with optional order and
purchase associations). Exposed over HTTP as a versioned JSON API
(`/api/v1/...`) authenticated with **Laravel Sanctum**.

## Business Context

`taskTFN` is a lightweight catalogue backend. The domain is two aggregate roots —
`Category` and `Product` — connected by a many-to-one relationship
(`product.category_id → categories.id`). Categories support nesting through a
self-referencing `parent_id`, and both resources expose a small set of CRUD
operations plus optional relation eager-loading (`includeCategoryParentIds`,
`includeCategoryProducts`, `includeProductOrders`, `includeProductPurchases`).

The API is the only entry point: there is no web UI. All responses are JSON,
paginated (10 per page), and shaped by Laravel **API Resources**
(`CategoryResource`, `ProductResource`) so the wire contract stays decoupled from
the persisted schema.

## Architecture

- **Framework**: Laravel 9 (`laravel/framework ^9.19`, PHP ^8.1).
- **Pattern**: resource-controller REST API (Laravel's idiomatic MVC, slimmed to
  HTTP layer + Eloquent models + API Resources).
- **Auth**: `laravel/sanctum` — `Route::middleware('auth:sanctum')` guards the
  `/api/v1/...` resource group.
- **Validation**: `ApiFormRequest` subclasses (`CategoryRequest`, `ProductRequest`)
  with method-aware rules; request payload keys are remapped to model column
  names in `prepareForValidation`.
- **Filtering**: `ApiFilter` base class with a declarative `safeParms` /
  `operatorMap` table (`eq`, `ne`, `lt`, `lte`, `gt`, `gte`, `lk`, `nlk`).
  `CategoryFilters` and `ProductFilters` declare the per-resource allow-list.
- **Serialization**: `JsonResource` subclasses translate models into camelCase
  DTOs (`categoryId`, `categoryName`, `productPrice`, ...).
- **Persistence**: MySQL via Eloquent; migrations live in `database/migrations/`.

```
app/
├── Console/            # Kernel
├── Exceptions/         # Handler
├── Filters/            # ApiFilter, CategoryFilters, ProductFilters
├── Http/
│   ├── Controllers/    # CategoryController, ProductController
│   ├── Middleware/     # Sanctum + default Laravel middleware
│   ├── Requests/       # ApiFormRequest, CategoryRequest, ProductRequest
│   └── Resources/      # CategoryResource, ProductResource (+Collection)
├── Models/             # Category, Product, User
├── Policies/           # CategoryPolicy
└── Providers/          # AppServiceProvider
routes/
├── api.php             # /api/v1/category, /api/v1/product (apiResource)
└── web.php, console.php, channels.php
database/
├── migrations/         # users, password_resets, failed_jobs,
│                       #   personal_access_tokens, categories, products
├── factories/          # CategoryFactory, ProductFactory, UserFactory
└── seeders/            # CategorySeeder, ProductSeeder, DatabaseSeeder
tests/                  # Feature/ExampleTest, Unit/ExampleTest
```

## API

Base URL: `/api/v1` (prefix registered in `routes/api.php`).

### Categories

| Method | Path | Description |
|---|---|---|
| GET | `/category` | List, filtered, paginated |
| GET | `/category/{id}` | Single category |
| POST | `/category` | Create |
| PUT | `/category` | Update (body includes `categoryId`) |
| DELETE | `/category/{id}` | Delete |

Query params: `categoryName[eq|ne|lk]`, `categoryParentId[eq|ne]`,
`includeCategoryParentIds`, `includeCategoryProducts`.

### Products

| Method | Path | Description |
|---|---|---|
| GET | `/product` | List, filtered, paginated |
| GET | `/product/{id}` | Single product |
| POST | `/product` | Create |
| PUT | `/product` | Update |
| DELETE | `/product/{id}` | Delete |

Query params: `productName[eq|lk]`, `productPrice[qt]`,
`productCategoryId[eq]`, `includeProductOrders`, `includeProductPurchases`.

Full request/response examples are documented in `docs/api.md` (migrated from the
legacy `DOC.md` scratch notes).

## Local Development

```bash
# 1. Install
composer install
cp .env.example .env && php artisan key:generate

# 2. Database (MySQL)
php artisan migrate --seed

# 3. Serve
php artisan serve
```

Environment configuration is driven by `.env.example` (all secrets left blank or
placeholder; never commit real credentials).

## Testing

```bash
vendor/bin/phpunit
```

## CI / Deployment

A Docker setup is not yet configured for this repository. When added, the standard
flow is `docker build → docker compose up → tests → health check`; see
`docs/deployment.md` once it exists.

## Documentation

- `docs/api.md` — endpoint contract, request/response payloads, validation rules.
- `docs/database.md` — schema (`categories`, `products`) and migration history.