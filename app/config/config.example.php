<?php
declare(strict_types=1);

/*
 * Copy this file to config.local.php and set the values from your host.
 * Never put real credentials in this tracked example file.
 */

return [
    'app' => [
        'environment' => 'production',
        'debug' => false,
    ],
    'database' => [
        'host' => 'localhost',
        'name' => 'CHANGE_ME_DATABASE',
        'username' => 'CHANGE_ME_USERNAME',
        'password' => 'CHANGE_ME_PASSWORD',
        'charset' => 'utf8mb4',
    ],
];
