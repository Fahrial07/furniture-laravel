<?php

namespace App\Models;

use App\Models\ProductGallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'slug'
    ];

    /**
     * Get all of the galleries for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(ProductGallery::class, 'product_id', 'id');
    }
}
