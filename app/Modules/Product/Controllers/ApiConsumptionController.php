<?php

namespace App\Modules\Product\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiConsumptionController extends Controller
{
    public function inventory(Request $request)
    {
        $error = null;
        $items = [];

        try {
            // Always use API for this demo to respect IFA (can toggle with query flag)
            $useApi = (bool) $request->boolean('use_api', true);

            if ($useApi) {
                // External API consumption (module boundary via HTTP)
                $endpoint = url('/api/inventory');
                $response = Http::timeout(10)->get($endpoint);

                if ($response->failed()) {
                    throw new \RuntimeException('Failed to fetch inventory via API');
                }

                $items = $response->json() ?? [];
            } else {
                $items = \App\Modules\Inventory\Models\Inventory::with('variations')
                    ->latest()
                    ->get()
                    ->toArray();
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return view('product::products.api-consume', [
            'items' => $items,
            'error' => $error,
            'used_http' => $useApi ?? true,
        ]);
    }
}

