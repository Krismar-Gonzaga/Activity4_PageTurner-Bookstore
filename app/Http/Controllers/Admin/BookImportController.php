<?php
// app/Http/Controllers/Admin/BookImportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookImport;
use App\Services\BookImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessBookImport;

class BookImportController extends Controller
{
    protected $importService;

    public function __construct(BookImportService $importService)
    {
        $this->importService = $importService;
    }

    public function showForm()
    {
        $recentImports = BookImport::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        return view('admin.books.import', compact('recentImports'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:10240', // 10MB max
            'duplicate_action' => 'required|in:skip,update'
        ]);

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('imports', $filename);

        $import = BookImport::create([
            'user_id' => auth()->id(),
            'filename' => $path,
            'original_name' => $file->getClientOriginalName(),
            'status' => 'pending',
            'duplicate_handling' => ['action' => $request->duplicate_action]
        ]);

        // Process synchronously for now, or dispatch job
        dispatch(new ProcessBookImport($import->id, $path, $request->duplicate_action));

        return response()->json([
            'success' => true,
            'import_id' => $import->id,
            'message' => 'Import started successfully'
        ]);
    }

    public function getStatus($id)
    {
        $import = BookImport::with('errorDetails')->findOrFail($id);
        
        return response()->json([
            'status' => $import->status,
            'total_rows' => $import->total_rows,
            'processed_rows' => $import->processed_rows,
            'successful_rows' => $import->successful_rows,
            'failed_rows' => $import->failed_rows,
            'errors' => $import->errorDetails,
            'completed_at' => $import->completed_at
        ]);
    }

    public function downloadTemplate()
    {
        $headers = ['ISBN', 'Title', 'Author', 'Price', 'Stock', 'Category', 'Description'];
        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            // Add sample data
            fputcsv($file, ['9780141182636', 'The Great Gatsby', 'F. Scott Fitzgerald', '12.99', '10', 'Fiction', 'A classic novel']);
            fputcsv($file, ['9780451524935', '1984', 'George Orwell', '9.99', '5', 'Fiction', 'Dystopian novel']);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="book_import_template.csv"'
        ]);
    }

    public function downloadErrorReport($id)
    {
        $import = BookImport::with('errorDetails')->findOrFail($id);
        
        $callback = function() use ($import) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Row Number', 'Errors', 'Data']);
            
            foreach ($import->errorDetails as $error) {
                fputcsv($file, [
                    $error->row_number,
                    implode('; ', $error->errors),
                    json_encode($error->row_data)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="import_errors_' . $import->id . '.csv"'
        ]);
    }
}