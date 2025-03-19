<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MultipleChoiceExam;
use App\Models\TrueOrFalseExam;
use App\Models\Attachment;
use App\Models\Subject;
use App\Models\Topic;

class Exam extends Model
{
    /** @use HasFactory<\Database\Factories\ExamFactory> */
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'title',
        'slug',
        'type',
        'size',
        'difficulty',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function topics()
    {
        return $this->belongsToMany(Topic::class);
    }

    public function attachments()
    {
        return $this->belongsToMany(Attachment::class, 'exam_attachment')->withTimestamps();
    }

    public function trueOrFalseExams()
    {
        return $this->hasMany(TrueOrFalseExam::class);
    }

    public function multipleChoiceExams()
    {
        return $this->hasMany(MultipleChoiceExam::class);
    }
}
