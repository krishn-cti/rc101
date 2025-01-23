<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleCourse extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id',
        'name',
        'section',
        'description',
        'owner_id',
        'status',
        'created_at'
    ];

    public function assignments()
    {
        return $this->hasMany(GoogleAssignment::class, 'course_id', 'course_id');
    }

    public function participants()
    {
        return $this->hasMany(GoogleCourseParticipant::class, 'course_id', 'course_id');
    }
}
