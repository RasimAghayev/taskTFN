GET

/api/v1/category  -- all category without parent_id

/api/v1/category/{id}  --  category id without parent_id

/api/v1/category?includeCategoryParentIds=true&categoryName[eq]=Mose Fay

/api/v1/category?includeCategoryParentIds=true&categoryParentId[eq]=12

/api/v1/category?includeCategoryParentIds=true   -- all category with parent_id

/api/v1/category/{id}?includeCategoryParentIds=true   -- category id with parent_id

/api/v1/category/105?includeCategoryParentIds=true&includeCategoryProducts=true   -- category id with products
POST

/api/v1/category

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


PUT

/api/v1/category

```json
{
    "categoryName": "Adelia Lowe",
    "categoryParentId": 17,
    "categoryId":61
}
```


GET

/api/v1/product  -- all product //myshop.local/models/ProductsModel.php -> line => 44

/api/v1/product/{id}  --  product id //myshop.local/models/ProductsModel.php -> line => 29

/api/v1/product?includeProductOrders=true   -- all product with orders

/api/v1/product/{id}?includeProductOrders=true   -- product id with orders

/api/v1/product?includeProductOrders=true&productName[eq]=Mose Fay

/api/v1/product?includeProductOrders=true&productPrice[qt]=1200

/api/v1/product/105?includeProductPurchases=true   -- product id with  Purchases

/api/v1/product/105?includeProductOrders=true&includeProductPurchases=true   -- product id with orders and Purchases

/api/v1/product?productCategoryId[eq]=105  --- //myshop.local/models/ProductsModel.php -> line => 6













POST

/api/v1/product

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

PUT

/api/v1/product

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


