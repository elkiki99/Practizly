<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attachment;
use App\Models\Subject;
use App\Models\Topic;

class Assignment extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'subject_id', 
        'topic_id', 
        'title',
        'description',
        'guidelines',
        'due_date', 
        'status',
    ];

    public function topic()
    {
        return $this->belongsToMany(Topic::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
