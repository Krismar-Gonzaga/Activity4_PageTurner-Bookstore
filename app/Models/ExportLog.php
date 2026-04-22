<?php
// app/Models/ExportLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportLog extends Model
{
    protected $table = 'export_logs';
    
    protected $fillable = [
        'user_id', 'export_type', 'format', 'filters', 'file_path',
        'total_records', 'status', 'error_message', 'completed_at'
    ];
    
    protected $casts = [
        'filters' => 'array',
        'completed_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}