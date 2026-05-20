<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookImportError extends Model
{
    protected $fillable = ['book_import_id', 'row_number', 'row_data', 'errors'];

    protected $casts = [
        'row_data' => 'array',
        'errors' => 'array'
    ];

    public function bookImport()
    {
        return $this->belongsTo(BookImport::class);
    }
}