<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'project_id',
        'uploaded_by',
        'document_number',
        'title',
        'document_type',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'description',
        'client_access',
    ];

    protected function casts(): array
    {
        return [
            'client_access' => 'boolean',
            'file_size' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}