<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public function discountGroup()
    {
        return $this->belongsToMany(ProductGroup::class, 'product_group_items', 'product_id', 'group_id');
    }
}
