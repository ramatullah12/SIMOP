<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FieldDocumentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentation_number',
        'project_id',
        'production_id',
        'uploaded_by',
        'title',
        'activity_date',
        'progress_percentage',
        'description',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'notes',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'progress_percentage' => 'integer',
        'file_size' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

