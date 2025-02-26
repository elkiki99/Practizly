<?php

namespace App\Models;

use App\Models\Topic;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Summary extends Model
{
    /** @use HasFactory<\Database\Factories\SummaryFactory> */
    use HasFactory;

    protected $fillable = [
        'topic_id', 
        'title',
        'size',
        'content',
        'attachment_id',
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
