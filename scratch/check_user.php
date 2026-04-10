<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$db = \TheFramework\App\Database::getInstance();

$tables = ['events', 'event_categories'];
foreach ($tables as $table) {
    echo "--- Table: $table ---\n";
    $db->query("DESCRIBE $table");
    $columns = $db->resultSet();
    foreach ($columns as $col) {
        echo "{$col['Field']} - {$col['Type']} - {$col['Null']} - {$col['Key']} - {$col['Default']} - {$col['Extra']}\n";
    }
    echo "\n";
}

$db->query("DESCRIBE users");
echo "USERS TABLE:\n";
print_r($db->resultSet());

$db->query("DESCRIBE model_has_roles");
echo "\nMODEL_HAS_ROLES TABLE:\n";
print_r($db->resultSet());

$db->query("SELECT * FROM roles");
echo "\nROLES TABLE CONTENT:\n";
print_r($db->resultSet());
