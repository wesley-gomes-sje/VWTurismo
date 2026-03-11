<?php

chdir(dirname(__DIR__));

ini_set('error_log', '/dev/null');

require_once __DIR__ . '/../vendor/autoload.php';

// JWT secret longo o suficiente para HS256 (>= 256 bits / 32 chars)
$_ENV['JWT_SECRET'] = 'vwturismo-test-secret-key-32-chars!!!';
