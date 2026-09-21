<?php

// Front Controller - Eleitor Consciente 2026

require_once __DIR__ . '/../src/bootstrap.php';

use App\Config\Env;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;

try {
    $request = new Request();
    $router = new Router();

    // Carrega definições de rotas
    require_once __DIR__ . '/../routes/api.php';
    require_once __DIR__ . '/../routes/web.php';

    // Despacha a requisição
    $router->dispatch($request);

} catch (Throwable $e) {
    error_log("Unhandled Application Exception: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
    \App\Support\Logger::error("Exceção não tratada: " . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()], 'app');

    $debug = Env::get('APP_DEBUG', false);
    $uri = $_SERVER['REQUEST_URI'] ?? '/';

    if (str_starts_with($uri, '/api/')) {
        $msg = $debug ? $e->getMessage() : 'Ocorreu um erro interno no servidor.';
        Response::error('SERVER_ERROR', $msg, $debug ? ['file' => $e->getFile(), 'line' => $e->getLine()] : [], 500);
    } else {
        http_response_code(500);
        if ($debug) {
            echo "<h1>Erro 500</h1><p>" . htmlspecialchars($e->getMessage()) . "</p><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            echo "<h1>Erro 500</h1><p>Ocorreu uma instabilidade temporária. Por favor, tente novamente em instantes.</p>";
        }
    }
}

