<?php

namespace App\Models;
use App\Models\Exam;

class OpenEndedExam extends Exam
{
    protected $fillable = [
        'exam_id',
        'question',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
