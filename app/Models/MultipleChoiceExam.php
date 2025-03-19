<?php

namespace App\Models;
use App\Models\Exam;

class MultipleChoiceExam extends Exam
{
    protected $fillable = [
        'exam_id',
        'question',
        'options',
        'correct_answer',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
