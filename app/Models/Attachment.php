<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    /** @use HasFactory<\Database\Factories\AttachmentFactory> */
    use HasFactory;

    protected $fillable = [
        'file_name',
        'file_path',
        'attachable_id',
        'attachable_type',
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_attachment')->withTimestamps();
    }

    public function summaries()
    {
        return $this->belongsToMany(Exam::class, 'summary_attachment')->withTimestamps();
    }
}
