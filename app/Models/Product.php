<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable=[
        "category_id",
        "name",
        "description",
        "price",
        "image",
        "status"
    ];

    public function productcategories(){
        return $this->belongsTo(Category::class);
    }
    public function productpurchases(){
        return $this->hasMany(Purchase::class,'product_id','id');
    }
}
