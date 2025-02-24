<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrumSpinner extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_drum_spinners';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'image',
        'status',
        'created_at'
    ];

    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('cms_images/' . $value);
        } else {
            return asset('admin/img/shop-img/no_image.png');
        }
    }
}
