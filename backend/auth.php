<?php
function checkAuth()
{
    if (!isset($_SESSION['idUser'])) {
        header("Location: /login");
        exit;
    }
}
