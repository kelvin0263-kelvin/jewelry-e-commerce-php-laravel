<?php

namespace App\Modules\Admin\Controllers;

use App\Modules\Admin\Services\CustomerSegmentationService; // Import the service

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiCustomerController extends Controller
{
        public function segmentation(CustomerSegmentationService $segmentationService)
    {
        $segments = $segmentationService->generateSegments();
        return response()->json($segments);
    }
}
