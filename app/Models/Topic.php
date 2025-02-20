<?php

namespace App\Models;

use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{
    /** @use HasFactory<\Database\Factories\TopicFactory> */
    use HasFactory;

    protected $fillable = [
        'subject_id', 
        'title', 
        'description', 
        'order', 
        'difficulty', 
        'estimated_study_time', 
        'status'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function exam()
    {
        return $this->belongsToMany(Exam::class);
    }
}
