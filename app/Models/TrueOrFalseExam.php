<?php

namespace App\Models;
use App\Models\Exam;

class TrueOrFalseExam extends Exam
{
    protected $fillable = [
        'exam_id',
        'question',
        'answer',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
