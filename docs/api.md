# API Reference

All endpoints are versioned under `/api/v1` and protected by
`auth:sanctum` (Laravel Sanctum personal-access tokens).

## Categories

### `GET /api/v1/category`

List categories. Filterable and paginated (10 per page).

| Parameter | Type | Operators | Description |
|---|---|---|---|
| `categoryName` | string | `eq`, `ne`, `lk` | Filter on `categories.name` |
| `categoryParentId` | int | `eq`, `ne` | Filter on `categories.parent_id` |
| `includeCategoryParentIds` | bool | — | Eager-load child categories |
| `includeCategoryProducts` | bool | — | Eager-load products belonging to each category |

- `GET /api/v1/category` — all categories **without** `parent_id`.
- `GET /api/v1/category/{id}` — category by id **without** `parent_id`.
- `GET /api/v1/category?includeCategoryParentIds=true` — all categories **with** `parent_id`.
- `GET /api/v1/category/{id}?includeCategoryParentIds=true` — category by id **with** `parent_id`.
- `GET /api/v1/category/105?includeCategoryParentIds=true&includeCategoryProducts=true` — category by id with parent ids and products.

### `POST /api/v1/category`

Create a category.

```json
{
  "categoryName": "Adelia Lowe"
}
```

or

```json
{
  "categoryName": "Adelia Lowe",
  "categoryParentId": 15
}
```

### `PUT /api/v1/category`

Update a category.

```json
{
  "categoryName": "Adelia Lowe",
  "categoryParentId": 17,
  "categoryId": 61
}
```

## Products

### `GET /api/v1/product`

List products. Filterable and paginated (10 per page).

| Parameter | Type | Operators | Description |
|---|---|---|---|
| `productName` | string | `eq`, `lk` | Filter on `products.name` |
| `productPrice` | numeric | `qt` | Filter on `products.price` |
| `productCategoryId` | int | `eq` | Filter on `products.category_id` |
| `includeProductOrders` | bool | — | Eager-load orders |
| `includeProductPurchases` | bool | — | Eager-load purchases |

- `GET /api/v1/product` — all products.
- `GET /api/v1/product/{id}` — product by id.
- `GET /api/v1/product?includeProductOrders=true` — all products with orders.
- `GET /api/v1/product/{id}?includeProductOrders=true` — product by id with orders.
- `GET /api/v1/product?includeProductOrders=true&productName[eq]=Mose Fay` — filtered, with orders.
- `GET /api/v1/product?includeProductOrders=true&productPrice[qt]=1200` — price-filtered, with orders.
- `GET /api/v1/product/105?includeProductPurchases=true` — product by id with purchases.
- `GET /api/v1/product/105?includeProductPurchases=true&includeProductOrders=true` — product by id with purchases and orders.
- `GET /api/v1/product?productCategoryId[eq]=105` — products for a category.

### `POST /api/v1/product`

Create a product.

```json
{
  "productCategoryId": 105,
  "productName": "Prof. Dannie Romaguera PhD",
  "productDescription": "qui",
  "productPrice": 2886.5,
  "productImage": "https://via.placeholder.com/640x480.png/00ee22?text=product+nesciunt",
  "productStatus": "0"
}
```

### `PUT /api/v1/product`

Update a product.

```json
{
  "productCategoryId": 105,
  "productName": "Prof. Dannie Romaguera PhD",
  "productDescription": "qui",
  "productPrice": 2886.5,
  "productImage": "https://via.placeholder.com/640x480.png/00ee22?text=product+nesciunt",
  "productStatus": "0"
}
```

## Response Shape

Single-resource responses (`show`) return an envelope:

```json
{
  "status": true,
  "message": "Category showed.",
  "data": { ... }
}
```

Collection responses (`index`) are Laravel paginated JSON resources.

## Validation

Rules are method-aware (`CategoryRequest`, `ProductRequest`):

- `POST`: `categoryName`/`productName` required, string.
- `PUT`: `categoryId`/`productId` required, integer, `exists:...`, plus the above.
- `DELETE`: `categoryId`/`productId` required, integer, `exists:...`.