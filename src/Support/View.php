<?php

namespace App\Support;

use RuntimeException;

class View
{
    private static string $basePath = __DIR__ . '/../../views/';

    public static function render(string $template, array $data = []): string
    {
        $file = self::$basePath . $template . '.php';
        if (!file_exists($file)) {
            throw new RuntimeException("View template [{$template}] não encontrada em {$file}.");
        }

        // Variável helper e para escape
        $e = fn(?string $val) => Sanitizer::escape($val);

        extract($data);
        ob_start();
        require $file;
        return ob_get_clean();
    }
}

