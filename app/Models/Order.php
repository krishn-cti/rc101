<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'transaction_id',
        'product_id',
        'quantity',
        'status',
        'created_at'
    ];

    public function productImages()
    {
        return $this->belongsTo(ProductImage::class, 'product_id', 'product_id')->select(['product_id', 'thumbnail']);
    }

    public function productDetails()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->select(['id', 'product_name', 'price', 'discount', 'category_id', 'sub_category_id']);
    }
}
