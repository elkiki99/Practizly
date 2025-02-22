<?php

namespace App\Models;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{
    /** @use HasFactory<\Database\Factories\TopicFactory> */
    use HasFactory;

    protected $fillable = [
        'subject_id', 
        'name', 
        'description', 
        'order', 
        'difficulty',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function exam()
    {
        return $this->belongsToMany(Exam::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
