<?php
// app/Models/BookImport.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookImport extends Model
{
    protected $fillable = [
        'user_id', 'filename', 'original_name', 'total_rows', 'processed_rows',
        'successful_rows', 'failed_rows', 'status', 'errors', 'duplicate_handling', 'completed_at'
    ];

    protected $casts = [
        'errors' => 'array',
        'duplicate_handling' => 'array',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function errorDetails()
    {
        return $this->hasMany(BookImportError::class);
    }
}

// app/Models/BookImportError.php
class BookImportError extends Model
{
    protected $fillable = ['book_import_id', 'row_number', 'row_data', 'errors'];

    protected $casts = [
        'row_data' => 'array',
        'errors' => 'array'
    ];
}