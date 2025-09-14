<?php
/**
 * Author: TAN CHUN KEAT
 * Date: 2025-09-15
 */
namespace App\Modules\Support\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            // Expose API field 'content' mapped from DB 'description'
            'content' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}