<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Embed extends Model
{
    use HasFactory;
        
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_embeds';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'embed_link',
        'type',
        'created_at'
    ];
}
