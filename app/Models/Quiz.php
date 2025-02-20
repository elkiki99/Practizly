<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    /** @use HasFactory<\Database\Factories\QuizFactory> */
    use HasFactory;

    protected $fillable = [
        'topic_id', 
        'title', 
        'total_questions', 
        'score', 
        'max_score', 
        'time_taken', 
        'difficulty'
    ];
    
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}
