<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    use HasFactory;

    public function productDetail()
    {
        return $this->hasOne(Product::class,'id','product_id');
    }
}
