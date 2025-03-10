<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'slug',
        'description',
        'type',
        'date',
        'note',
        'status',
        'subject_id'
    ];

    public function topics() : BelongsToMany
    {
        return $this->belongsToMany(Topic::class);
    }

    public function subject() : BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
