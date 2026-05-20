<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$export = new \App\Models\ExportJob();
$export->user_id = 1;
$export->format = 'xlsx';
$export->filters = [];
$export->selected_fields = ['title', 'author'];
$export->status = 'pending';
$export->save();
echo "Created export: " . $export->id . "\n";

$service = app(\App\Services\BookExportService::class);
try {
    $path = $service->export($export->id, [], ['title', 'author'], 'xlsx', 1);
    echo "File created at: " . $path . "\n";
    echo "File exists: " . (file_exists($path) ? 'yes' : 'no') . "\n";
    $export->refresh();
    echo "Export status: " . $export->status . "\n";
    echo "Filename: " . $export->filename . "\n";
    
    // Test download path
    $normalizedPath = str_replace('/', DIRECTORY_SEPARATOR, $path);
    echo "Normalized path: " . $normalizedPath . "\n";
    echo "Normalized exists: " . (file_exists($normalizedPath) ? 'yes' : 'no') . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}