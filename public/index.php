<?php

/**
 * Bootstrap para quando o web root é public/.
 * Ajusta o cwd para a raiz do projeto e delega para o index.php principal.
 */
chdir(dirname(__DIR__));

require_once dirname(__DIR__) . '/index.php';
