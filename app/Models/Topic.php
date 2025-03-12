<?php

namespace App\Models;

use App\Models\Exam;
use App\Models\Event;
use App\Models\Subject;
use App\Models\Assignment;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Topic extends Model
{
    /** @use HasFactory<\Database\Factories\TopicFactory> */
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'name',
    ];

    public function events() : BelongsToMany
    {
        return $this->belongsToMany(Event::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function summaries()
    {
        return $this->hasMany(Summary::class);
    }

    public function directAttachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function assignmentAttachments()
    {
        return $this->hasManyThrough(Attachment::class, Assignment::class, 'topic_id', 'attachable_id')
            ->where('attachable_type', Assignment::class);
    }

    public function getAllAttachmentsAttribute()
    {
        return $this->directAttachments->merge($this->assignmentAttachments);
    }
}
