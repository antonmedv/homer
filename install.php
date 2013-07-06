<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

$db = new PDO(HOMER_DNS);
$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

try {

    $db->exec("DROP TABLE IF EXISTS queue");

    $db->exec("
    CREATE TABLE IF NOT EXISTS queue (
        id INTEGER PRIMARY KEY,
        url TEXT UNIQUE,
        deep INTEGER
    )");

    $db->exec("DROP TABLE IF EXISTS indexes");

    $db->exec("
    CREATE VIRTUAL TABLE indexes USING fts3 (
        url TEXT UNIQUE,
        title TEXT,
        body TEXT
    )");

    echo "Migrated successfully.\n";

} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}

