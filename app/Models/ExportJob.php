<?php
// app/Models/ExportJob.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportJob extends Model
{
    protected $fillable = [
        'user_id', 'filename', 'file_path', 'format', 'filters', 
        'selected_fields', 'total_records', 'processed_records', 
        'progress', 'status', 'error_message', 'completed_at'
    ];

    protected $casts = [
        'filters' => 'array',
        'selected_fields' => 'array',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}