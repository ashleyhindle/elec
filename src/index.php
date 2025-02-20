<?php

declare(strict_types=1);

use App\App;

require_once file_exists(__DIR__ . '/vendor/autoload.php')
    ? __DIR__ . '/vendor/autoload.php'
    : __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(realpath(__DIR__ . '/..'));
$dotenv->load();

$db = new SQLite3('whc.db', SQLITE3_OPEN_READONLY);
$db->enableExceptions(true);

(new App(db: $db, ip: $argv[1] ?? '86.2.94.106'))->prompt();
