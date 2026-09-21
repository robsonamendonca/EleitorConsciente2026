<?php

namespace App\Config;

class Env
{
    private static bool $loaded = false;

    public static function load(string $path = __DIR__ . '/../../.env'): void
    {
        if (self::$loaded) {
            return;
        }

        if (file_exists($path)) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }
                if (str_contains($line, '=')) {
                    [$name, $value] = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value);
                    $value = trim($value, '"\'');
                    if (!isset($_SERVER[$name]) && !isset($_ENV[$name])) {
                        putenv(sprintf('%s=%s', $name, $value));
                        $_ENV[$name] = $value;
                        $_SERVER[$name] = $value;
                    }
                }
            }
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();
        $val = getenv($key);
        if ($val === false) {
            $val = $_ENV[$key] ?? $_SERVER[$key] ?? $default;
        }

        if ($val === 'true' || $val === '(true)') {
            return true;
        }
        if ($val === 'false' || $val === '(false)') {
            return false;
        }
        if ($val === 'null' || $val === '(null)') {
            return null;
        }

        return $val;
    }
}

