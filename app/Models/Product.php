<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku',
        'product_name',
        'related_name',
        'description',
        'category_id',
        'sub_category_id',
        'price',
        'discount',
        'status',
        'created_at'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->select(['id', 'category_name']);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id')->select(['id', 'sub_category_name']);
    }

    public function productImages()
    {
        return $this->hasOne(ProductImage::class, 'product_id', 'id')->select(['product_id', 'thumbnail', 'images']);
    }

    public function productReviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id', 'id')
            ->select(['product_id', 'user_id', 'rating', 'review_text', 'created_at'])
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'profile_image');
                }
            ]);
    }
}
