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
        'owner_id',
        'title',
        'description',
        'max_points',
        'due_date',
        'due_time',
        'attachment_link',
        'curriculum_ids',
        'status',
        'submitted_at',
        'is_pushed_on_google',
        'created_at'
    ];

    public function course()
    {
        return $this->belongsTo(GoogleCourse::class, 'course_id', 'course_id');
    }
}
