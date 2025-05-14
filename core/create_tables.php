<?php

$config = require 'config/database.php';


$tempDb = new PDO(
    "{$config['db_system']}:host={$config['db_host']}",
    $config['db_username'],
    $config['db_password']
);
$tempDb->exec("CREATE DATABASE IF NOT EXISTS {$config['db_name']}");

// Then use your Database class normally
$tempDb->query("
    USE {$config['db_name']};
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        firstname VARCHAR(100),
        lastname VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");
