<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Seller extends User
{
    use HasFactory;
    public function products()
    {
        return $this->HasMany(Product::class);
    }
}
