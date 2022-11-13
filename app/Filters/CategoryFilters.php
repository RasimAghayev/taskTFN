<?php

namespace App\Filters;

use App\Filters\ApiFilter;

class CategoryFilters extends ApiFilter
{
    protected $safeParms=[
        'categoryName'=>['eq','ne'],
        'categoryParentId'=>['eq','ne'],
    ];
    protected $columnMap=[
        'categoryName'=>'name',
        'categoryParentId'=>'parent_id',
    ];
}
