<?php

/**
 * Entry point da aplicação quando o web root aponta para public/.
 * Muda o cwd para a raiz do projeto para que todos os includes
 * relativos (templates, assets etc.) continuem funcionando.
 */
chdir(dirname(__DIR__));

require_once dirname(__DIR__) . '/index.php';
