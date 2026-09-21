<?php

namespace App\Domain\Nvidia;

use App\Config\Env;
use App\Http\Request;
use App\Http\Response;
use App\Support\NvidiaClient;

/**
 * Controlador para endpoints de IA via NVIDIA AI Endpoints.
 *
 * Fornece endpoints para:
 * - Verificação de status da integração
 * - Listagem de modelos disponíveis
 * - Análise de candidatos via IA
 * - Explicações sobre cargos políticos
 * - Chat genérico com modelos NVIDIA
 * - Geração de embeddings
 */
class NvidiaController
{
    private NvidiaClient $client;

    public function __construct()
    {
        $this->client = new NvidiaClient();
    }

    /**
     * GET /api/v1/nvidia/status
     * Retorna o status da configuração da API NVIDIA.
     */
    public function status(Request $request): void
    {
        $configured = $this->client->isConfigured();

        Response::success([
            'configured' => $configured,
            'default_model' => $configured ? Env::get('NVIDIA_DEFAULT_MODEL', 'meta/llama-3.1-8b-instruct') : null,
            'base_url' => $configured ? Env::get('NVIDIA_API_BASE_URL', 'https://integrate.api.nvidia.com/v1') : null,
        ], [], 200);
    }

    /**
     * GET /api/v1/nvidia/models
     * Lista os modelos disponíveis na API NVIDIA.
     */
    public function models(Request $request): void
    {
        try {
            $models = $this->client->listModels();

            $formatted = array_map(fn($model) => [
                'id' => $model['id'] ?? null,
                'name' => $model['name'] ?? $model['id'] ?? null,
                'owned_by' => $model['owned_by'] ?? null,
            ], $models);

            Response::success($formatted, ['total' => count($formatted)]);
        } catch (\RuntimeException $e) {
            Response::error('NVIDIA_API_ERROR', $e->getMessage(), [], 502);
        }
    }

    /**
     * POST /api/v1/nvidia/analyze-candidate
     * Analisa um candidato usando IA e retorna um resumo factual.
     *
     * Body JSON:
     * {
     *   "candidate": { ... dados do candidato ... }
     * }
     */
    public function analyzeCandidate(Request $request): void
    {
        $data = $request->post();

        if (empty($data['candidate'])) {
            Response::error('VALIDATION_ERROR', 'O campo "candidate" é obrigatório.', [], 422);
            return;
        }

        try {
            $result = $this->client->analyzeCandidate($data['candidate']);

            Response::success([
                'analysis' => $result['content'],
                'model' => $result['model'],
                'usage' => $result['usage'],
            ]);
        } catch (\RuntimeException $e) {
            Response::error('NVIDIA_API_ERROR', $e->getMessage(), [], 502);
        }
    }

    /**
     * POST /api/v1/nvidia/explain-office
     * Gera uma explicação sobre um cargo político.
     *
     * Body JSON:
     * {
     *   "office_name": "Presidente da República"
     * }
     */
    public function explainOffice(Request $request): void
    {
        $data = $request->post();

        if (empty($data['office_name'])) {
            Response::error('VALIDATION_ERROR', 'O campo "office_name" é obrigatório.', [], 422);
            return;
        }

        try {
            $result = $this->client->explainOffice($data['office_name']);

            Response::success([
                'explanation' => $result['content'],
                'model' => $result['model'],
                'usage' => $result['usage'],
            ]);
        } catch (\RuntimeException $e) {
            Response::error('NVIDIA_API_ERROR', $e->getMessage(), [], 502);
        }
    }

    /**
     * POST /api/v1/nvidia/chat
     * Envia uma mensagem para um modelo NVIDIA e retorna a resposta.
     *
     * Body JSON:
     * {
     *   "prompt": "Sua mensagem aqui",
     *   "model": "meta/llama-3.1-8b-instruct",  // opcional
     *   "max_tokens": 1024,                       // opcional
     *   "temperature": 0.7                        // opcional
     * }
     */
    public function chat(Request $request): void
    {
        $data = $request->post();

        if (empty($data['prompt'])) {
            Response::error('VALIDATION_ERROR', 'O campo "prompt" é obrigatório.', [], 422);
            return;
        }

        try {
            $options = [];
            if (isset($data['max_tokens'])) {
                $options['max_tokens'] = (int) $data['max_tokens'];
            }
            if (isset($data['temperature'])) {
                $options['temperature'] = (float) $data['temperature'];
            }

            $result = $this->client->chatCompletion(
                $data['prompt'],
                [],
                $data['model'] ?? null,
                $options
            );

            Response::success([
                'response' => $result['content'],
                'model' => $result['model'],
                'finish_reason' => $result['finish_reason'],
                'usage' => $result['usage'],
            ]);
        } catch (\RuntimeException $e) {
            Response::error('NVIDIA_API_ERROR', $e->getMessage(), [], 502);
        }
    }

    /**
     * POST /api/v1/nvidia/embeddings
     * Gera embeddings para textos.
     *
     * Body JSON:
     * {
     *   "input": "texto ou ["array", "de textos"]",
     *   "model": "nvidia/nv-embedqa-e5-v5"  // opcional
     * }
     */
    public function embeddings(Request $request): void
    {
        $data = $request->post();

        if (empty($data['input'])) {
            Response::error('VALIDATION_ERROR', 'O campo "input" é obrigatório.', [], 422);
            return;
        }

        try {
            $result = $this->client->embeddings(
                $data['input'],
                $data['model'] ?? null
            );

            Response::success([
                'embeddings' => $result,
                'count' => count($result),
            ]);
        } catch (\RuntimeException $e) {
            Response::error('NVIDIA_API_ERROR', $e->getMessage(), [], 502);
        }
    }
}
