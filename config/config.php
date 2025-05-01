<?php
define('JWT_SECRET_KEY', getenv('JWT_SECRET_KEY'));
define('JWT_ALGORITHM', getenv('JWT_ALGORITHM'));
define('MYSQL_DB_HOST', getenv('MYSQL_DB_HOST'));
define('MYSQL_DATABASE', getenv('MYSQL_DATABASE'));
define('MYSQL_USER', getenv('MYSQL_USER'));
define('MYSQL_PASSWORD', getenv('MYSQL_PASSWORD'));

try {
    $pdo = new PDO('mysql:host=' . MYSQL_DB_HOST . ';dbname=' . MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error connecting to database: " . $e->getMessage());
}
