<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use Illuminate\Support\Facades\DB;
$connection = DB::connection();
echo 'Connection: '.$connection->getName().PHP_EOL;
echo 'Database: '.$connection->getDatabaseName().PHP_EOL;
echo 'Driver: '.$connection->getDriverName().PHP_EOL;
echo 'Book count: '.DB::table('books')->count().PHP_EOL;