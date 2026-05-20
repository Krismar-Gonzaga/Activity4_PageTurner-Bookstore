<?php
// Minimal boot — no full framework boot needed
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$key = $_ENV['OPENAI_API_KEY'] ?? getenv('OPENAI_API_KEY') ?: '';
$prefixOk = str_starts_with($key ?? '', 'sk-');
$lenOk    = strlen($key ?? '') > 20;

echo "Key starts with 'sk-': " . ($prefixOk ? 'YES' : 'NO') . PHP_EOL;
echo "Key length: " . strlen($key ?? '') . PHP_EOL;
echo "Looks valid: " . ($prefixOk && $lenOk ? 'YES' : 'NO') . PHP_EOL;
echo "Key preview: " . substr($key ?? '', 0, 12) . '...' . PHP_EOL;
