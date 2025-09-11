<?php

namespace SupportModule\Sdk;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use SupportModule\Sdk\Exceptions\SupportSdkException;

class SupportClient
{
    protected GuzzleClient $http;
    protected string $baseUrl;
    protected string $apiPrefix;
    protected ?string $token;
    protected int $timeout;
    protected bool $debug;

    public function __construct(array $config = [])
    {
        $this->baseUrl = rtrim((string)($config['base_url'] ?? ''), '/');
        $this->apiPrefix = '/' . trim((string)($config['api_prefix'] ?? '/api'), '/');
        $this->token = $config['token'] ?? null;
        $this->timeout = (int)($config['timeout'] ?? 10);
        $this->debug = (bool)($config['debug'] ?? false);

        if ($this->baseUrl === '') {
            // Try Laravel config if available
            if (function_exists('config')) {
                $fromConfig = (string) (config('support-sdk.base_url') ?? '');
                if ($fromConfig !== '') {
                    $this->baseUrl = rtrim($fromConfig, '/');
                }
            }
        }

        $this->http = new GuzzleClient([
            'base_uri' => $this->baseUrl !== '' ? ($this->baseUrl . '/') : '',
            'http_errors' => false,
            'timeout' => $this->timeout,
        ]);
    }

    // Tickets (user)
    public function listTickets(array $query = []): array
    {
        return $this->get('/support/tickets', $query);
    }

    public function createTicket(array $data): array
    {
        return $this->post('/support/tickets', $data);
    }

    public function showTicket(int|string $ticketId): array
    {
        return $this->get("/support/tickets/{$ticketId}");
    }

    public function updateTicket(int|string $ticketId, array $data): array
    {
        return $this->put("/support/tickets/{$ticketId}", $data);
    }

    public function replyTicket(int|string $ticketId, array $data): array
    {
        return $this->post("/support/tickets/{$ticketId}/reply", $data);
    }

    public function closeTicket(int|string $ticketId): array
    {
        return $this->post("/support/tickets/{$ticketId}/close");
    }

    // Chat (user)
    public function startChat(array $payload = []): array
    {
        return $this->post('/support/chat/start', $payload);
    }

    public function getQueueStatus(int|string $conversationId): array
    {
        return $this->get("/support/chat/queue/{$conversationId}");
    }

    public function leaveQueue(int|string $conversationId): array
    {
        return $this->post("/support/chat/{$conversationId}/leave");
    }

    public function terminateConversation(int|string $conversationId): array
    {
        return $this->post("/support/chat/{$conversationId}/terminate");
    }

    public function userConversations(array $query = []): array
    {
        return $this->get('/support/chat/conversations', $query);
    }

    public function createConversation(array $payload): array
    {
        return $this->post('/support/chat/conversations', $payload);
    }

    public function conversation(int|string $conversationId): array
    {
        return $this->get("/support/chat/conversations/{$conversationId}");
    }

    public function messages(int|string $conversationId, array $query = []): array
    {
        return $this->get("/support/chat/conversations/{$conversationId}/messages", $query);
    }

    public function sendMessage(int|string|null $conversationId, array $payload): array
    {
        if ($conversationId === null) {
            return $this->post('/support/chat/messages', $payload);
        }
        return $this->post("/support/chat/conversations/{$conversationId}/messages", $payload);
    }

    public function faq(): array
    {
        return $this->get('/support/faq');
    }

    public function categories(): array
    {
        return $this->get('/support/categories');
    }

    // Admin - Tickets
    public function adminListTickets(array $query = []): array
    {
        return $this->get('/admin/support/tickets', $query);
    }

    public function adminShowTicket(int|string $ticketId): array
    {
        return $this->get("/admin/support/tickets/{$ticketId}");
    }

    public function adminUpdateTicket(int|string $ticketId, array $data): array
    {
        return $this->put("/admin/support/tickets/{$ticketId}", $data);
    }

    public function adminAssignTicket(int|string $ticketId, array $data): array
    {
        return $this->post("/admin/support/tickets/{$ticketId}/assign", $data);
    }

    public function adminReplyTicket(int|string $ticketId, array $data): array
    {
        return $this->post("/admin/support/tickets/{$ticketId}/reply", $data);
    }

    public function adminCloseTicket(int|string $ticketId): array
    {
        return $this->post("/admin/support/tickets/{$ticketId}/close");
    }

    public function adminHistory(int|string $ticketId): array
    {
        return $this->get("/admin/support/tickets/{$ticketId}/history");
    }

    // Admin - Chat
    public function adminChatConversations(array $query = []): array
    {
        return $this->get('/admin/support/chat/conversations', $query);
    }

    public function adminChatShowConversation(int|string $conversationId): array
    {
        return $this->get("/admin/support/chat/conversations/{$conversationId}");
    }

    public function adminChatMessages(int|string $conversationId, array $query = []): array
    {
        return $this->get("/admin/support/chat/conversations/{$conversationId}/messages", $query);
    }

    public function adminChatSendMessage(int|string $conversationId, array $payload): array
    {
        return $this->post("/admin/support/chat/conversations/{$conversationId}/messages", $payload);
    }

    public function adminChatTransfer(int|string $conversationId, array $payload): array
    {
        return $this->post("/admin/support/chat/conversations/{$conversationId}/transfer", $payload);
    }

    // Admin - Queue
    public function adminQueueIndex(array $query = []): array
    {
        return $this->get('/admin/support/queue', $query);
    }

    public function adminQueueTakeNext(array $payload = []): array
    {
        return $this->post('/admin/support/queue/take', $payload);
    }

    public function adminQueueAssign(int|string $queueId, array $payload): array
    {
        return $this->post("/admin/support/queue/{$queueId}/assign", $payload);
    }

    // Low-level HTTP helpers
    protected function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, ['query' => $query]);
    }

    protected function post(string $path, array $json = []): array
    {
        $opts = [];
        if ($json !== []) {
            $opts['json'] = $json;
        }
        return $this->request('POST', $path, $opts);
    }

    protected function put(string $path, array $json = []): array
    {
        $opts = [];
        if ($json !== []) {
            $opts['json'] = $json;
        }
        return $this->request('PUT', $path, $opts);
    }

    protected function request(string $method, string $path, array $options = []): array
    {
        $uri = $this->buildPath($path);
        $headers = [
            'Accept' => 'application/json',
        ];
        if (!empty($options['json'])) {
            $headers['Content-Type'] = 'application/json';
        }
        if ($this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        $options['headers'] = array_merge($headers, $options['headers'] ?? []);
        $options['debug'] = $this->debug;

        try {
            $response = $this->http->request($method, $uri, $options);
        } catch (RequestException $e) {
            $resp = $e->getResponse();
            if ($resp instanceof ResponseInterface) {
                $this->throwFor($resp);
            }
            throw new SupportSdkException($e->getMessage(), $e->getCode(), $e);
        }

        $status = $response->getStatusCode();
        if ($status < 200 || $status >= 300) {
            $this->throwFor($response);
        }

        return $this->toArray($response);
    }

    protected function buildPath(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        return ltrim($this->apiPrefix . $path, '/');
    }

    protected function toArray(ResponseInterface $response): array
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $body = (string) $response->getBody();
        if (stripos($contentType, 'application/json') !== false || $this->isJson($body)) {
            $decoded = json_decode($body, true);
            return is_array($decoded) ? $decoded : ['data' => $decoded];
        }
        return ['data' => $body];
    }

    protected function throwFor(ResponseInterface $response): void
    {
        $status = $response->getStatusCode();
        $body = (string) $response->getBody();
        $message = '';
        $context = [];
        $decoded = null;
        if ($this->isJson($body)) {
            $decoded = json_decode($body, true);
            $message = $decoded['message'] ?? ($decoded['error'] ?? '');
            $context = is_array($decoded) ? $decoded : [];
        }
        throw SupportSdkException::httpError($status, $message ?: $body, $context);
    }

    protected function isJson(string $str): bool
    {
        if ($str === '') return false;
        json_decode($str);
        return json_last_error() === JSON_ERROR_NONE;
    }
}

