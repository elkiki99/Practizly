<?php

namespace App\Models;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    /** @use HasFactory<\Database\Factories\ExamFactory> */
    use HasFactory;

    protected $fillable = [
        'topic_id', 
        'title', 
        'exam_type',
        // 'exam_date',    
        // 'study_plan', 
        // 'estimated_study_time', 
        // 'progress', 
        // 'notes'
    ];

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
}
