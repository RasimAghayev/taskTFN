# Database Schema

MySQL via Laravel Eloquent. Migrations live in `database/migrations/`.

## `categories`

| Column | Type | Notes |
|---|---|---|
| `id` | `bigIncrements` | PK |
| `name` | `string` | Category display name |
| `parent_id` | `unsignedBigInteger` | Self-reference; `0` for root categories |
| `created_at` / `updated_at` | `timestamps` | |

Migration: `2022_11_12_224705_create_categories_table.php`.

## `products`

| Column | Type | Notes |
|---|---|---|
| `id` | `bigIncrements` | PK |
| `category_id` | `unsignedBigInteger` | FK → `categories.id` |
| `name` | `string` | Product name |
| `description` | `longText` | Product description |
| `price` | `float(8,2)` | Unit price |
| `image` | `string` | Image URL |
| `status` | `enum('0','1')` | `1` = active, default |
| `created_at` / `updated_at` | `timestamps` | |

Migration: `2022_11_13_055623_create_products_table.php`.

## Other tables

Laravel defaults: `users`, `password_resets`, `failed_jobs`,
`personal_access_tokens` (Sanctum).

## Seeders

`CategorySeeder`, `ProductSeeder`, `DatabaseSeeder` — `database/seeders/`.

## Eloquent Relations

- `Category::categoryparentids()` — `hasMany(Category, 'parent_id')` (children).
- `Category::categoryproducts()` — `hasMany(Product, 'category_id')`.
- `Product::productcategories()` — `belongsTo(Category)`.
- `Product::productpurchases()` — `hasMany(Purchase, 'product_id')` (Purchase model is
  referenced but not yet scaffolded in `app/Models/`).