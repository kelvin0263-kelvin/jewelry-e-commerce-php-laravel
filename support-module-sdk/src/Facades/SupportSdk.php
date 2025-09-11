<?php

namespace SupportModule\Sdk\Facades;

use Illuminate\Support\Facades\Facade;
use SupportModule\Sdk\SupportClient;

/**
 * @method static array listTickets(array $query = [])
 * @method static array createTicket(array $data)
 * @method static array showTicket(int|string $ticketId)
 * @method static array updateTicket(int|string $ticketId, array $data)
 * @method static array replyTicket(int|string $ticketId, array $data)
 * @method static array closeTicket(int|string $ticketId)
 * @method static array startChat(array $payload = [])
 * @method static array getQueueStatus(int|string $conversationId)
 * @method static array leaveQueue(int|string $conversationId)
 * @method static array terminateConversation(int|string $conversationId)
 * @method static array userConversations(array $query = [])
 * @method static array createConversation(array $payload)
 * @method static array conversation(int|string $conversationId)
 * @method static array messages(int|string $conversationId, array $query = [])
 * @method static array sendMessage(int|string|null $conversationId, array $payload)
 * @method static array faq()
 * @method static array categories()
 * @method static array adminListTickets(array $query = [])
 * @method static array adminShowTicket(int|string $ticketId)
 * @method static array adminUpdateTicket(int|string $ticketId, array $data)
 * @method static array adminAssignTicket(int|string $ticketId, array $data)
 * @method static array adminReplyTicket(int|string $ticketId, array $data)
 * @method static array adminCloseTicket(int|string $ticketId)
 * @method static array adminHistory(int|string $ticketId)
 * @method static array adminChatConversations(array $query = [])
 * @method static array adminChatShowConversation(int|string $conversationId)
 * @method static array adminChatMessages(int|string $conversationId, array $query = [])
 * @method static array adminChatSendMessage(int|string $conversationId, array $payload)
 * @method static array adminChatTransfer(int|string $conversationId, array $payload)
 * @method static array adminQueueIndex(array $query = [])
 * @method static array adminQueueTakeNext(array $payload = [])
 * @method static array adminQueueAssign(int|string $queueId, array $payload)
 * @see SupportModule\Sdk\SupportClient
 */
class SupportSdk extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SupportClient::class;
    }
}

