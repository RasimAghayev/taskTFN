<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable=[
        "name",
        "parent_id"
    ];

    public function categoryparentids(){
        return $this->hasMany(Category::class,'parent_id','id');
    }
    public function categoryproducts(){
        return $this->hasMany(Product::class,'category_id','id');
    }
}
