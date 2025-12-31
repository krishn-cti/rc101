<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;
        
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_curriculums';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'title',
        'embed_link',
        'type',
        'file_type',
        'sequence',
        'number_of_days',
        'description',
        'created_at'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->select(['id', 'category_name', 'description']);
    }
}
