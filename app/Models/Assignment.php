<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attachment;
use App\Models\Topic;

class Assignment extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'slug',
        'title',
        'description',
        'guidelines',
        'due_date', 
        'status',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function subject()
    {
        return $this->hasOneThrough(Subject::class, Topic::class, 'id', 'id', 'topic_id', 'subject_id');
    }
}
