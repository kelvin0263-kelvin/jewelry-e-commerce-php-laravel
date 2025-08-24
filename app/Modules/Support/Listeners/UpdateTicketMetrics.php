<?php

namespace App\Modules\Support\Listeners;

use App\Modules\Support\Events\TicketCreated;
use App\Modules\Support\Events\TicketStatusChanged;
use App\Modules\Support\Events\TicketResolved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateTicketMetrics implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the TicketCreated event.
     */
    public function handleTicketCreated(TicketCreated $event): void
    {
        try {
            $this->updateDashboardMetrics();
            $this->updatePriorityMetrics($event->ticket->priority, 'increment');

            Log::info('Ticket metrics updated for new ticket', [
                'ticket_id' => $event->ticket->id,
                'priority' => $event->ticket->priority
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update metrics for new ticket', [
                'ticket_id' => $event->ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the TicketStatusChanged event.
     */
    public function handleTicketStatusChanged(TicketStatusChanged $event): void
    {
        try {
            $this->updateStatusMetrics($event->oldStatus, $event->newStatus);
            $this->updateDashboardMetrics();

            Log::info('Ticket metrics updated for status change', [
                'ticket_id' => $event->ticket->id,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update metrics for status change', [
                'ticket_id' => $event->ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the TicketResolved event.
     */
    public function handleTicketResolved(TicketResolved $event): void
    {
        try {
            $this->updateResolutionMetrics($event->resolutionTimeHours);
            $this->updateDashboardMetrics();

            Log::info('Ticket metrics updated for resolution', [
                'ticket_id' => $event->ticket->id,
                'resolution_time_hours' => $event->resolutionTimeHours
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update metrics for ticket resolution', [
                'ticket_id' => $event->ticket->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update dashboard metrics cache
     */
    private function updateDashboardMetrics(): void
    {
        // Clear dashboard metrics cache so they get refreshed
        Cache::forget('support.dashboard.metrics');
        Cache::forget('support.tickets.count.open');
        Cache::forget('support.tickets.count.total');
    }

    /**
     * Update priority-specific metrics
     */
    private function updatePriorityMetrics(string $priority, string $action): void
    {
        $cacheKey = "support.tickets.priority.{$priority}";
        
        if ($action === 'increment') {
            Cache::increment($cacheKey, 1);
        } elseif ($action === 'decrement') {
            Cache::decrement($cacheKey, 1);
        }
    }

    /**
     * Update status-specific metrics
     */
    private function updateStatusMetrics(string $oldStatus, string $newStatus): void
    {
        Cache::decrement("support.tickets.status.{$oldStatus}", 1);
        Cache::increment("support.tickets.status.{$newStatus}", 1);
    }

    /**
     * Update resolution time metrics
     */
    private function updateResolutionMetrics(int $resolutionTimeHours): void
    {
        // Update average resolution time
        $currentAvg = Cache::get('support.resolution.average', 0);
        $resolvedCount = Cache::get('support.resolution.count', 0);
        
        $newCount = $resolvedCount + 1;
        $newAvg = (($currentAvg * $resolvedCount) + $resolutionTimeHours) / $newCount;
        
        Cache::put('support.resolution.average', $newAvg, now()->addHours(24));
        Cache::put('support.resolution.count', $newCount, now()->addHours(24));
    }
}
