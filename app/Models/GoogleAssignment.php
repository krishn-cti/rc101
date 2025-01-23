<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleAssignment extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'assignment_id',
        'course_id',
        'title',
        'description',
        'due_date',
        'status',
        'created_at'
    ];

    public function course()
    {
        return $this->belongsTo(GoogleCourse::class, 'course_id', 'course_id');
    }
}
