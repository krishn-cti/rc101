<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cms_bots';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'bot_type_id',
        'weight_class_id',
        'design_type',
        'description',
        'start_date',
        'image',
        'created_by',
        'status',
        'created_at'
    ];

    public function botType()
    {
        return $this->belongsTo(BotType::class, 'bot_type_id', 'id')->select(['id', 'name']);
    }

    public function weightClass()
    {
        return $this->belongsTo(WeightClassCategory::class, 'weight_class_id', 'id')->select(['id', 'name']);
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id')->select(['id', 'name']);
    }
}
