<?php

namespace App\Filters;

use App\Filters\ApiFilter;

class ProductFilters extends ApiFilter
{
    protected $safeParms=[
        'productCategoryId'=>['eq','lt','lte','gt','gte','ne'],
        'productName'=>['eq','ne'],
        'productDescription'=>['eq','ne'],
        'productPrice'=>['eq','lt','lte','gt','gte','ne'],
        'productStatus'=>['eq','ne'],
    ];
    protected $columnMap=[
        'productCategoryId'=>'category_id',
        'productName'=>'name',
        'productDescription'=>'description',
        'productPrice'=>'price',
        'productStatus'=>'status',
    ];
}
