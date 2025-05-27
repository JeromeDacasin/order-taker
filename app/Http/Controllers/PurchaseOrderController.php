<?php

namespace App\Http\Controllers;

use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(protected PurchaseOrderService $service)
    {
        
    }

    public function store(Request $request) {
        return $this->service->store($request);
    }
}
