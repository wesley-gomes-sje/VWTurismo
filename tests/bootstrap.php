<?php

/**
 * Muda o cwd para a raiz do projeto para que os require_once relativos
 * dos models funcionem corretamente nos testes.
 * Desativa error_log para evitar que PHPUnit capture stderr de processos
 * isolados como erros de teste.
 */
chdir(dirname(__DIR__));

ini_set('error_log', '/dev/null');

require_once __DIR__ . '/../vendor/autoload.php';
