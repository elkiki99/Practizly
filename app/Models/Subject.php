<?php

namespace App\Models;

use App\Models\User;
use App\Models\Topic;
use App\Models\Assignment;
use App\Models\Attachment;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'name',
        'description', 
        'color', 
        'goal', 
        'completion_percentage', 
        'is_favorite'
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class, 'subject_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'subject_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function assignments()
    // {
    //     return $this->hasManyThrough(Assignment::class, Topic::class);
    // }
}
