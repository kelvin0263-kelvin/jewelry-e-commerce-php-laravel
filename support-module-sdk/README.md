Support Module SDK

Lightweight PHP/Laravel SDK to consume the Support module API (tickets, chat, FAQ) from another Laravel application.

Install (local path repo)
- Add a path repository in your consumer app composer.json:
  {
    "repositories": [
      { "type": "path", "url": "../support-module-sdk" }
    ],
    "require": {
      "app/support-module-sdk": "*"
    }
  }
- Run: composer require app/support-module-sdk:* --update-with-all-dependencies

Laravel setup
- The service provider auto-registers via package discovery.
- Publish config: php artisan vendor:publish --tag=support-sdk-config

Config
- SUPPORT_API_BASE_URL: Base URL of the app exposing the Support API (e.g. https://your-app.test)
- SUPPORT_API_TOKEN: API token (Sanctum/Personal Access Token) used for Authorization: Bearer ...
- SUPPORT_API_PREFIX: Defaults to /api
- SUPPORT_API_TIMEOUT: Request timeout in seconds

Quick start (Laravel)
  use SupportModule\Sdk\Facades\SupportSdk; 

  // List current user tickets
  $tickets = SupportSdk::listTickets();

  // Create ticket
  $ticket = SupportSdk::createTicket([
      'subject' => 'Issue with order',
      'message' => 'I have a problem with my order #1234',
      'category' => 'orders'
  ]);

  // Chat: start and send a message
  $conversation = SupportSdk::startChat(['topic' => 'General question']);
  SupportSdk::sendMessage($conversation['id'] ?? $conversation['data']['id'] ?? null, [
      'message' => 'Hello, is anyone there?'
  ]);

Endpoints covered (user)
- GET    /api/support/tickets
- POST   /api/support/tickets
- GET    /api/support/tickets/{id}
- PUT    /api/support/tickets/{id}
- POST   /api/support/tickets/{id}/reply
- POST   /api/support/tickets/{id}/close
- POST   /api/support/chat/start
- GET    /api/support/chat/queue/{conversationId}
- POST   /api/support/chat/{conversationId}/leave
- POST   /api/support/chat/{conversationId}/terminate
- GET    /api/support/chat/conversations
- POST   /api/support/chat/conversations
- GET    /api/support/chat/conversations/{id}
- GET    /api/support/chat/conversations/{id}/messages
- POST   /api/support/chat/conversations/{id}/messages
- POST   /api/support/chat/messages
- GET    /api/support/faq
- GET    /api/support/categories

Endpoints covered (admin)
- GET    /api/admin/support/tickets
- GET    /api/admin/support/tickets/{id}
- PUT    /api/admin/support/tickets/{id}
- POST   /api/admin/support/tickets/{id}/assign
- POST   /api/admin/support/tickets/{id}/reply
- POST   /api/admin/support/tickets/{id}/close
- GET    /api/admin/support/tickets/{id}/history
- GET    /api/admin/support/chat/conversations
- GET    /api/admin/support/chat/conversations/{id}
- GET    /api/admin/support/chat/conversations/{id}/messages
- POST   /api/admin/support/chat/conversations/{id}/messages
- POST   /api/admin/support/chat/conversations/{id}/transfer
- GET    /api/admin/support/queue
- POST   /api/admin/support/queue/take
- POST   /api/admin/support/queue/{id}/assign

Non-Laravel usage
  $client = new SupportModule\Sdk\SupportClient([
    'base_url' => 'https://your-app.test',
    'token' => 'YOUR_TOKEN',
    'api_prefix' => '/api',
    'timeout' => 10,
  ]);
  $tickets = $client->listTickets();

