<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesReportRequest;
use App\Http\Resources\SaleResource;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $service
    ) {}

    public function sales(SalesReportRequest $request): JsonResponse
    {
        $sales = $this->service->getSalesReport($request->validated());

        $resource = SaleResource::collection($sales)->resolve();

        return $this->success($resource);
    }
}
