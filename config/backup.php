<?php

return [
    'backup' => [
        'destination' => [
            'disks' => ['local'],
            'filename_prefix' => 'backup_',
        ],
        'sources' => [
            'database' => true,
            'files' => [
                storage_path('app/public'),
            ],
            'config' => [
                config_path(),
            ],
        ],
        'temporary_directory' => storage_path('app/backup-temp'),
        'encryption' => [
            'enabled' => false,
            'password' => env('BACKUP_ENCRYPTION_PASSWORD'),
        ],
    ],
    'cleanup' => [
        'strategy' => 'default',
        'default_strategy' => [
            'keep_allBackups' => 7,
            'keep_dailyBackups' => 7,
            'keep_weeklyBackups' => 4,
            'keep_monthlyBackups' => 12,
            'keep_yearlyBackups' => 0,
        ],
    ],
];