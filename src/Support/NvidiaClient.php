<?php

namespace App\Support;

use App\Config\Env;

/**
 * Cliente para NVIDIA AI Endpoints API.
 * 
 * Fornece métodos para interagir com os modelos de IA disponíveis
 * na plataforma NVIDIA (NIM - NVIDIA Inference Microservices).
 * 
 * @see https://build.nvidia.com/
 * @see https://docs.api.nvidia.com/
 */
class NvidiaClient
{
    private string $apiKey;
    private string $baseUrl;
    private string $defaultModel;
    private int $maxTokens;
    private float $temperature;

    public function __construct()
    {
        $this->apiKey = Env::get('NVIDIA_API_KEY', '');
        $this->baseUrl = rtrim(Env::get('NVIDIA_API_BASE_URL', 'https://integrate.api.nvidia.com/v1'), '/');
        $this->defaultModel = Env::get('NVIDIA_DEFAULT_MODEL', 'meta/llama-3.1-8b-instruct');
        $this->maxTokens = (int) Env::get('NVIDIA_MAX_TOKENS', 1024);
        $this->temperature = (float) Env::get('NVIDIA_TEMPERATURE', 0.7);
    }

    /**
     * Verifica se a API key está configurada.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Retorna a lista de modelos disponíveis na API.
     *
     * @return array Lista de modelos
     * @throws \RuntimeException Se a API key não estiver configurada
     * @throws \RuntimeException Se houver erro na comunicação com a API
     */
    public function listModels(): array
    {
        $this->ensureConfigured();

        $response = $this->request('GET', '/models');

        return $response['data'] ?? [];
    }

    /**
     * Envia uma mensagem para um modelo de linguagem (Chat Completions).
     *
     * @param string $prompt Mensagem do usuário
     * @param array $messages Histórico de mensagens (opcional)
     * @param string|null $model Modelo a utilizar (opcional, usa o padrão se não especificado)
     * @param array $options Opções adicionais (max_tokens, temperature, etc.)
     * @return array Resposta formatada com conteúdo gerado
     * @throws \RuntimeException Se houver erro na requisição
     */
    public function chatCompletion(
        string $prompt,
        array $messages = [],
        ?string $model = null,
        array $options = []
    ): array {
        $this->ensureConfigured();

        $model = $model ?? $this->defaultModel;

        // Se não houver mensagens prévias, cria a estrutura padrão
        if (empty($messages)) {
            $messages = [
                ['role' => 'user', 'content' => $prompt]
            ];
        }

        $body = array_merge([
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
            'stream' => false,
        ], $options);

        $response = $this->request('POST', '/chat/completions', $body);

        // Extrai a resposta do formato padrão OpenAI-compatible
        $choice = $response['choices'][0] ?? null;

        return [
            'id' => $response['id'] ?? null,
            'model' => $response['model'] ?? $model,
            'content' => $choice['message']['content'] ?? '',
            'finish_reason' => $choice['finish_reason'] ?? null,
            'usage' => $response['usage'] ?? [],
        ];
    }

    /**
     * Gera embeddings para um ou mais textos.
     *
     * @param string|array $input Texto ou array de textos
     * @param string|null $model Modelo de embeddings (opcional)
     * @return array Lista de embeddings
     * @throws \RuntimeException Se houver erro na requisição
     */
    public function embeddings(string|array $input, ?string $model = null): array
    {
        $this->ensureConfigured();

        $model = $model ?? 'nvidia/nv-embedqa-e5-v5';
        $input = is_string($input) ? [$input] : $input;

        $body = [
            'model' => $model,
            'input' => $input,
            'input_type' => 'query',
            'encoding_format' => 'float',
        ];

        $response = $this->request('POST', '/embeddings', $body);

        return $response['data'] ?? [];
    }

    /**
     * Analisa um candidato usando IA, gerando um resumo neutro de suas informações públicas.
     *
     * @param array $candidateData Dados do candidato
     * @return array Resposta com análise gerada pela IA
     * @throws \RuntimeException Se houver erro na requisição
     */
    public function analyzeCandidate(array $candidateData): array
    {
        $this->ensureConfigured();

        $prompt = $this->buildCandidateAnalysisPrompt($candidateData);

        $messages = [
            [
                'role' => 'system',
                'content' => 'Você é um assistente neutro e imparcial que fornece resumos factuais sobre candidatos eleitorais. '
                    . 'Apresente apenas informações verificáveis e públicas. '
                    . 'Não emita julgamentos, recomendações ou opiniões políticas. '
                    . 'Seja objetivo, claro e conciso. '
                    . 'Responda sempre em português brasileiro.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ];

        return $this->chatCompletion('', $messages, null, [
            'temperature' => 0.3,
            'max_tokens' => 512,
        ]);
    }

    /**
     * Gera uma explicação sobre um cargo político para cidadãos.
     *
     * @param string $officeName Nome do cargo (ex: "Presidente da República")
     * @return array Resposta com a explicação gerada
     * @throws \RuntimeException Se houver erro na requisição
     */
    public function explainOffice(string $officeName): array
    {
        $this->ensureConfigured();

        $messages = [
            [
                'role' => 'system',
                'content' => 'Você é um professor de ciências políticas que explica cargos públicos de forma simples e didática. '
                    . 'Use linguagem acessível para qualquer cidadão. '
                    . 'Seja neutro e imparcial. '
                    . 'Responda em português brasileiro.'
            ],
            [
                'role' => 'user',
                'content' => "Explique de forma simples e objetiva o cargo de **{$officeName}** no Brasil: "
                    . "quem elege, principais atribuições, mandato e qualquer informação relevante para um eleitor."
            ]
        ];

        return $this->chatCompletion('', $messages, null, [
            'temperature' => 0.5,
            'max_tokens' => 400,
        ]);
    }

    /**
     * Realiza uma requisição HTTP para a API da NVIDIA.
     *
     * @param string $method Método HTTP (GET, POST)
     * @param string $endpoint Endpoint da API
     * @param array|null $body Corpo da requisição (para POST)
     * @return array Resposta decodificada
     * @throws \RuntimeException Se houver erro na requisição
     */
    private function request(string $method, string $endpoint, ?array $body = null): array
    {
        $url = $this->baseUrl . $endpoint;

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($body !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new \RuntimeException("NVIDIA API: Erro de conexão - {$error}");
        }

        $data = json_decode($response, true);

        if ($httpCode >= 400) {
            $message = $data['error']['message'] ?? $data['message'] ?? "HTTP {$httpCode}";
            throw new \RuntimeException("NVIDIA API: {$message} (HTTP {$httpCode})");
        }

        return $data ?? [];
    }

    /**
     * Valida se a API key está configurada.
     *
     * @throws \RuntimeException Se a API key não estiver configurada
     */
    private function ensureConfigured(): void
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException(
                'NVIDIA API key não configurada. Defina a variável NVIDIA_API_KEY no arquivo .env'
            );
        }
    }

    /**
     * Constrói o prompt para análise de candidato.
     *
     * @param array $candidateData Dados do candidato
     * @return string Prompt formatado
     */
    private function buildCandidateAnalysisPrompt(array $candidateData): string
    {
        $name = $candidateData['name'] ?? 'Não informado';
        $ballotNumber = $candidateData['ballot_number'] ?? 'Não informado';
        $party = $candidateData['party_abbreviation'] ?? $candidateData['party'] ?? 'Não informado';
        $office = $candidateData['office_name'] ?? $candidateData['office'] ?? 'Não informado';
        $state = $candidateData['state_code'] ?? 'Não informado';
        $education = $candidateData['education'] ?? 'Não informado';
        $age = $candidateData['age'] ?? 'Não informado';
        $gender = $candidateData['gender'] ?? 'Não informado';
        $race = $candidateData['race_ethnicity'] ?? 'Não informado';
        $maritalStatus = $candidateData['marital_status'] ?? 'Não informado';
        $occupation = $candidateData['occupation'] ?? 'Não informado';

        return "Gere um resumo factual e neutro sobre o seguinte candidato às eleições de 2026:\n\n"
            . "**Nome:** {$name}\n"
            . "**Número:** {$ballotNumber}\n"
            . "**Partido:** {$party}\n"
            . "**Cargo:** {$office}\n"
            . "**UF:** {$state}\n"
            . "**Idade:** {$age}\n"
            . "**Gênero:** {$gender}\n"
            . "**Raça/Cor:** {$race}\n"
            . "**Estado Civil:** {$maritalStatus}\n"
            . "**Escolaridade:** {$education}\n"
            . "**Profissão:** {$occupation}\n\n"
            . "Apresente as informações de forma objetiva, sem emitir opiniões ou recomendações. "
            . "Se dados estiverem faltando, informe que não estão disponíveis. "
            . "Limite-se a no máximo 3 parágrafos curtos.";
    }
}
