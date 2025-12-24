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
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 50);
        unset($filters['per_page']);

        $sales = $this->service->getSalesReport($filters, $perPage);

        $resource = SaleResource::collection($sales)->response()->getData(true);

        return $this->success($resource);
    }
}
