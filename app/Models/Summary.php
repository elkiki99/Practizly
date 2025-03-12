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
        'title',
        'slug',
        'size',
        'subject_id',
    ];

    public function topics()
    {
        return $this->belongsToMany(Topic::class);
    }

    public function attachments()
    {
        return $this->belongsToMany(Attachment::class, 'summary_attachment')->withTimestamps();
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
