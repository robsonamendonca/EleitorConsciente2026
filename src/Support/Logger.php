<?php

namespace App\Support;

class Logger
{
    private static string $logDir = __DIR__ . '/../../storage/logs';

    public static function log(string $level, string $message, array $context = [], string $channel = 'app'): void
    {
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0775, true);
        }

        $date = date('Y-m-d H:i:s');
        $level = strtoupper($level);

        // Remove tokens e senhas do contexto por segurança (LGPD/Security)
        $safeContext = self::sanitizeContext($context);
        $contextStr = !empty($safeContext) ? ' ' . json_encode($safeContext, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        $line = "[{$date}] [{$level}] {$message}{$contextStr}" . PHP_EOL;

        $file = self::$logDir . "/{$channel}.log";
        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = [], string $channel = 'app'): void
    {
        self::log('INFO', $message, $context, $channel);
    }

    public static function warning(string $message, array $context = [], string $channel = 'app'): void
    {
        self::log('WARNING', $message, $context, $channel);
    }

    public static function error(string $message, array $context = [], string $channel = 'app'): void
    {
        self::log('ERROR', $message, $context, $channel);
    }

    private static function sanitizeContext(array $context): array
    {
        $sensitiveKeys = ['password', 'pass', 'secret', 'token', 'api_key', 'authorization'];
        foreach ($context as $key => $val) {
            if (is_array($val)) {
                $context[$key] = self::sanitizeContext($val);
            } elseif (in_array(strtolower($key), $sensitiveKeys, true)) {
                $context[$key] = '***REDACTED***';
            }
        }
        return $context;
    }
}

