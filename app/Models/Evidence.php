<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasActionLogs;
use Illuminate\Support\Facades\Storage;

class Evidence extends Model
{
    use HasFactory, HasActionLogs;

    protected $table = 'evidences';

    protected $fillable = [
        'case_file_id',
        'original_name',
        'stored_name',
        'mime_type',
        'size',
        'hash',
        'uploaded_by',
    ];

    // Append computed attributes when the model is serialized
    protected $appends = ['file_name', 'file_path', 'uploaded_at'];

    /* Relationships */
    public function caseFile(): BelongsTo
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /* Accessors */
    public function getFileNameAttribute(): ?string
    {
        return $this->original_name;
    }

    public function getFilePathAttribute(): ?string
    {
        return $this->stored_name ? Storage::url('evidences/' . $this->stored_name) : null;
    }

    public function getUploadedAtAttribute()
    {
        return $this->created_at;
    }
}






