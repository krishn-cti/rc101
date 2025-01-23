<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleCourseParticipant extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'created_at'
    ];

    public function course()
    {
        return $this->belongsTo(GoogleCourse::class, 'course_id', 'course_id');
    }
}
