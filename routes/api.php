<?php

use App\Domain\Candidate\CandidateController;
use App\Domain\Election\ElectionController;
use App\Domain\Health\HealthController;
use App\Domain\Import\ImportController;
use App\Http\Router;

/** @var Router $router */

// Rotas Públicas da API
$router->get('/api/v1/health', [HealthController::class, 'check']);
$router->get('/api/v1/elections', [ElectionController::class, 'index']);
$router->get('/api/v1/elections/{id}', [ElectionController::class, 'show']);
$router->get('/api/v1/offices', [ElectionController::class, 'offices']);
$router->get('/api/v1/parties', [ElectionController::class, 'parties']);

$router->get('/api/v1/candidates', [CandidateController::class, 'apiIndex']);
$router->get('/api/v1/candidates/{id}', [CandidateController::class, 'apiShow']);
$router->get('/api/v1/candidates/{id}/sources', [CandidateController::class, 'apiSources']);
$router->get('/api/v1/candidates/{id}/proposals', [CandidateController::class, 'apiProposals']);
$router->get('/api/v1/candidates/{id}/records', [CandidateController::class, 'apiRecords']);

$router->get('/api/v1/imports/status', [ImportController::class, 'status']);

// Rotas NVIDIA AI Endpoints
use App\Domain\Nvidia\NvidiaController;
$router->get('/api/v1/nvidia/status', [NvidiaController::class, 'status']);
$router->get('/api/v1/nvidia/models', [NvidiaController::class, 'models']);
$router->post('/api/v1/nvidia/analyze-candidate', [NvidiaController::class, 'analyzeCandidate']);
$router->post('/api/v1/nvidia/explain-office', [NvidiaController::class, 'explainOffice']);
$router->post('/api/v1/nvidia/chat', [NvidiaController::class, 'chat']);
$router->post('/api/v1/nvidia/embeddings', [NvidiaController::class, 'embeddings']);

// Rotas Administrativas Protegidas
$router->get('/api/v1/admin/imports', [ImportController::class, 'adminImports']);
$router->get('/api/v1/admin/imports/{id}', [ImportController::class, 'adminImportDetail']);
$router->post('/api/v1/admin/imports', [ImportController::class, 'adminTriggerImport']);
$router->get('/api/v1/admin/import-errors', [ImportController::class, 'adminImportErrors']);

