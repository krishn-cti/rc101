<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaContent extends Model
{
    use HasFactory;
        
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media_contents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'type',
        'link',
        'created_at'
    ];
}
