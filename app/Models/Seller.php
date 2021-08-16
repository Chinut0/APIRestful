<?php

namespace App\Models;

use App\Models\Product;
use App\Scopes\SellerScope;
use App\Transformers\SellerTransformer;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Seller extends User
{
    use HasFactory;

    public $transformer = SellerTransformer::class;


    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new SellerScope);
    }


    public function products()
    {
        return $this->HasMany(Product::class);
    }
}
