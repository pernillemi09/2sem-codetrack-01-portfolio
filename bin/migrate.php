<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database\Migration;

$migration = new Migration();
$migration->migrate();

echo "Migrations completed successfully!\n";
