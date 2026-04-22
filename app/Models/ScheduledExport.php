<?php
// app/Models/ScheduledExport.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledExport extends Model
{
    protected $table = 'scheduled_exports';
    
    protected $fillable = [
        'name', 'type', 'format', 'filters', 'schedule',
        'recipients', 'is_active', 'last_run_at', 'next_run_at'
    ];
    
    protected $casts = [
        'filters' => 'array',
        'recipients' => 'array',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'is_active' => 'boolean'
    ];
}