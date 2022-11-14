<?php

namespace App\Filters;

use App\Filters\ApiFilter;

class ProductFilters extends ApiFilter
{
    protected $safeParms=[
        'productCategoryId'=>['eq','lt','lte','gt','gte','ne'],
        'productName'=>['eq','ne','lk','nlk'],
        'productDescription'=>['eq','ne','lk','nlk'],
        'productPrice'=>['eq','lt','lte','gt','gte','ne','lk'],
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
