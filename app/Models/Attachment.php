<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
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
        'size'
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

    public function getFormattedSizeAttribute()
    {
        $size = $this->size;
        
        if ($size >= 1048576) {
            return round($size / 1048576, 2) . ' MB';
        }
    
        return round($size / 1024, 2) . ' KB';
    }
}
