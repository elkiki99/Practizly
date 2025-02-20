<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    /** @use HasFactory<\Database\Factories\ExamFactory> */
    use HasFactory;

    protected $fillable = [
        'topic_id', 
        'title', 
        'exam_date', 
        'study_plan', 
        'estimated_study_time', 
        'progress', 
        'notes'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
