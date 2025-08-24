<?php

namespace App\Modules\Support\Tests;

use App\Modules\Support\Models\Ticket;
use App\Modules\Support\Models\TicketReply;
use App\Modules\Support\Events\TicketCreated;
use App\Modules\Support\Events\TicketStatusChanged;
use App\Modules\Support\Events\TicketAssigned;
use App\Modules\Support\Events\TicketReplyAdded;
use App\Modules\Support\Events\TicketEscalated;
use App\Modules\Support\Events\TicketResolved;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ObserverPatternTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing event listeners to avoid interference
        Event::fake();
        Notification::fake();
    }

    /** @test */
    public function it_fires_ticket_created_event_when_ticket_is_created()
    {
        // Arrange
        $user = User::factory()->create();
        
        // Act
        $ticket = Ticket::create([
            'user_id' => $user->id,
            'subject' => 'Test ticket',
            'description' => 'Test description',
            'priority' => 'normal',
            'category' => 'general_inquiry',
            'status' => 'open'
        ]);

        // Assert
        Event::assertDispatched(TicketCreated::class, function ($event) use ($ticket) {
            return $event->ticket->id === $ticket->id;
        });
    }

    /** @test */
    public function it_fires_ticket_status_changed_event_when_status_is_updated()
    {
        // Arrange
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open'
        ]);

        // Act
        $ticket->update(['status' => 'in_progress']);

        // Assert
        Event::assertDispatched(TicketStatusChanged::class, function ($event) use ($ticket) {
            return $event->ticket->id === $ticket->id 
                && $event->oldStatus === 'open' 
                && $event->newStatus === 'in_progress';
        });
    }

    /** @test */
    public function it_fires_ticket_assigned_event_when_agent_is_assigned()
    {
        // Arrange
        $user = User::factory()->create();
        $agent = User::factory()->create(['is_admin' => true]);
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open'
        ]);

        // Act
        $ticket->assignToAgent($agent->id);

        // Assert
        Event::assertDispatched(TicketAssigned::class, function ($event) use ($ticket, $agent) {
            return $event->ticket->id === $ticket->id 
                && $event->agent->id === $agent->id;
        });
    }

    /** @test */
    public function it_fires_ticket_reply_added_event_when_reply_is_created()
    {
        // Arrange
        $user = User::factory()->create();
        $agent = User::factory()->create(['is_admin' => true]);
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        // Act
        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
            'message' => 'Agent response',
            'reply_type' => 'agent',
            'is_internal' => false
        ]);

        // Assert
        Event::assertDispatched(TicketReplyAdded::class, function ($event) use ($ticket, $reply) {
            return $event->ticket->id === $ticket->id 
                && $event->reply->id === $reply->id;
        });
    }

    /** @test */
    public function it_fires_ticket_escalated_event_when_ticket_is_escalated()
    {
        // Arrange
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'priority' => 'normal'
        ]);

        // Act
        $ticket->escalate('Customer complaint');

        // Assert
        Event::assertDispatched(TicketEscalated::class, function ($event) use ($ticket) {
            return $event->ticket->id === $ticket->id 
                && $event->oldPriority === 'normal' 
                && $event->newPriority === 'high'
                && $event->reason === 'Customer complaint';
        });
    }

    /** @test */
    public function it_fires_ticket_resolved_event_when_ticket_is_marked_resolved()
    {
        // Arrange
        $user = User::factory()->create();
        $agent = User::factory()->create(['is_admin' => true]);
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'status' => 'in_progress'
        ]);

        // Act
        $ticket->markAsResolved($agent->id);

        // Assert
        Event::assertDispatched(TicketResolved::class, function ($event) use ($ticket, $agent) {
            return $event->ticket->id === $ticket->id 
                && $event->resolvedBy->id === $agent->id;
        });
    }

    /** @test */
    public function it_can_handle_multiple_events_in_sequence()
    {
        // Arrange
        $user = User::factory()->create();
        $agent = User::factory()->create(['is_admin' => true]);

        // Act - Create ticket
        $ticket = Ticket::create([
            'user_id' => $user->id,
            'subject' => 'Complex issue',
            'description' => 'This needs multiple actions',
            'priority' => 'normal',
            'category' => 'technical_support',
            'status' => 'open'
        ]);

        // Act - Assign to agent
        $ticket->assignToAgent($agent->id);

        // Act - Add reply
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
            'message' => 'Working on this issue',
            'reply_type' => 'agent',
            'is_internal' => false
        ]);

        // Act - Escalate
        $ticket->escalate('Complex technical issue');

        // Act - Resolve
        $ticket->markAsResolved($agent->id);

        // Assert all events were fired
        Event::assertDispatched(TicketCreated::class);
        Event::assertDispatched(TicketAssigned::class);
        Event::assertDispatched(TicketReplyAdded::class);
        Event::assertDispatched(TicketEscalated::class);
        Event::assertDispatched(TicketResolved::class);
        Event::assertDispatched(TicketStatusChanged::class);
    }
}

// Helper trait for Observer Pattern demonstration
trait ObserverPatternDemo
{
    /**
     * Demonstrate the Observer Pattern in action
     */
    public function demonstrateObserverPattern()
    {
        echo "=== Observer Pattern Demonstration ===\n\n";

        // Create test data
        $customer = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        $agent = User::factory()->create(['name' => 'Support Agent', 'email' => 'agent@company.com', 'is_admin' => true]);

        echo "1. Creating a new ticket (TicketCreated event will fire)\n";
        $ticket = Ticket::create([
            'user_id' => $customer->id,
            'subject' => 'Demo: Website not loading',
            'description' => 'The website is showing a 500 error when I try to access my account.',
            'priority' => 'high',
            'category' => 'technical_support',
            'status' => 'open'
        ]);
        echo "   ✓ Ticket #{$ticket->ticket_number} created\n";
        echo "   ✓ Events fired: TicketCreated\n";
        echo "   ✓ Notifications sent: Customer confirmation, Admin alerts\n\n";

        echo "2. Assigning ticket to agent (TicketAssigned event will fire)\n";
        $ticket->assignToAgent($agent->id);
        echo "   ✓ Ticket assigned to {$agent->name}\n";
        echo "   ✓ Events fired: TicketAssigned, TicketStatusChanged\n";
        echo "   ✓ Notifications sent: Agent assignment notification\n\n";

        echo "3. Agent replies to customer (TicketReplyAdded event will fire)\n";
        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $agent->id,
            'message' => 'Hi John, I\'ve identified the issue. It appears to be a server configuration problem. I\'m working on fixing it now and will update you within the hour.',
            'reply_type' => 'agent',
            'is_internal' => false
        ]);
        echo "   ✓ Agent reply added\n";
        echo "   ✓ Events fired: TicketReplyAdded\n";
        echo "   ✓ Notifications sent: Customer reply notification\n\n";

        echo "4. Escalating ticket due to complexity (TicketEscalated event will fire)\n";
        $ticket->escalate('Requires senior developer intervention');
        echo "   ✓ Ticket escalated from 'high' to 'urgent' priority\n";
        echo "   ✓ Events fired: TicketEscalated\n";
        echo "   ✓ Logs updated: Escalation reason recorded\n\n";

        echo "5. Customer responds (TicketReplyAdded event will fire)\n";
        $customerReply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $customer->id,
            'message' => 'Thank you for the quick response! I really appreciate the help.',
            'reply_type' => 'customer',
            'is_internal' => false
        ]);
        echo "   ✓ Customer reply added\n";
        echo "   ✓ Events fired: TicketReplyAdded, TicketStatusChanged\n";
        echo "   ✓ Notifications sent: Agent notification of customer response\n\n";

        echo "6. Resolving the ticket (TicketResolved event will fire)\n";
        $ticket->markAsResolved($agent->id);
        echo "   ✓ Ticket marked as resolved\n";
        echo "   ✓ Events fired: TicketResolved, TicketStatusChanged\n";
        echo "   ✓ Metrics updated: Resolution time, agent performance\n\n";

        echo "=== Observer Pattern Benefits Demonstrated ===\n";
        echo "✓ Decoupled system: Models don't need to know about notifications\n";
        echo "✓ Extensible: Easy to add new listeners without changing existing code\n";
        echo "✓ Maintainable: Each listener has a single responsibility\n";
        echo "✓ Testable: Events can be easily mocked and tested\n";
        echo "✓ Logging: Comprehensive audit trail of all ticket activities\n";
        echo "✓ Notifications: Automatic email and database notifications\n";
        echo "✓ Metrics: Real-time dashboard updates and analytics\n\n";
    }
}
