<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subject;
use App\Models\Topic;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'date',
        'note',
        'status',
        'topic_id'
    ];

    public function topic() : BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function subject() : hasOneThrough
    {
        return $this->hasOneThrough(Subject::class, Topic::class, 'id', 'id', 'topic_id', 'subject_id');
    }
}
