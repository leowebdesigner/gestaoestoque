<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesReportRequest;
use App\Http\Resources\SaleResource;
use App\Services\ReportService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReportController extends Controller
{
    public function sales(SalesReportRequest $request, ReportService $service): AnonymousResourceCollection
    {
        $sales = $service->getSalesReport($request->validated());

        return SaleResource::collection($sales);
    }
}
