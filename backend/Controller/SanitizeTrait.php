<?php

namespace App\Controller;

trait SanitizeTrait
{
    protected function sanitizeString(?string $string): string
    {
        return htmlspecialchars(strip_tags($string ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
