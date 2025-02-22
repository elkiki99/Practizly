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
        'subject_id', 
        'topic_id', 
        'title',
        'description',
        'guidelines',
        'attachments', 
        'due_date', 
        'status',
    ];

    public function topics()
    {
        return $this->belongsToMany(Topic::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
