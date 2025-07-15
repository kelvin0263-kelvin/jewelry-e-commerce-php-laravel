<?php

namespace App\Http\Controllers\Api;

use App\Services\CustomerSegmentationService; // Import the service

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
        public function segmentation(CustomerSegmentationService $segmentationService)
    {
        $segments = $segmentationService->generateSegments();
        return response()->json($segments);
    }
}
