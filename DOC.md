GET

/api/v1/category  -- all category without parent_id

/api/v1/category/{id}  --  category id without parent_id


/api/v1/category?includeCategoryParentIds=true   -- all category with parent_id

/api/v1/category/{id}?includeCategoryParentIds=true   -- category id with parent_id

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
