<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGroup extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','discount'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_group_items', 'group_id', 'product_id');
    }
}
