<?php

use App\Domain\Web\WebController;
use App\Http\Router;

/** @var Router $router */

$router->get('/', [WebController::class, 'home']);
$router->get('/candidatos', [WebController::class, 'candidates']);
$router->get('/candidato/{id}', [WebController::class, 'candidateDetail']);
$router->get('/cola-eleitoral', [WebController::class, 'checklist']);
$router->get('/fontes', [WebController::class, 'sources']);
$router->get('/metodologia', [WebController::class, 'methodology']);
$router->get('/sobre', [WebController::class, 'about']);
$router->get('/privacidade', [WebController::class, 'privacy']);
$router->get('/status', [WebController::class, 'status']);

