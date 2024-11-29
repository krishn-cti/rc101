<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'thumbnail',
        'images',
        'created_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getThumbnailAttribute($value)
    {
        if ($value) {
            return asset('products/' . $value);
        } else {
            // If $value is null or empty, return the URL of the default user avatar image
            return asset('admin/img/shop-img/no_product_img.png');
        }
    }

    public function getImagesAttribute($value)
    {
        if ($value) {
            $images = explode(',', $value);
            $imageUrls = [];

            foreach ($images as $image) {
                $imageUrls[] = asset('products/' . trim($image));
            }

            return $imageUrls;
        } else {
            // If $value is null or empty, return the URL of the default user avatar image
            // return [asset('admin/img/shop-img/no_product_img.png')];
        }
    }
}
