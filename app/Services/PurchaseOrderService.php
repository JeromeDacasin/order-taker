<?php

namespace App\Services;

use App\Http\Resources\PurchaseOrderCollection;
use App\Models\PurchaseItem;
use App\Models\PurchaseOrder;
use Exception;

class PurchaseOrderService
{
    public function __construct(protected PurchaseOrder $order, protected PurchaseItem $items)
    {
        
    }

    public function store($request) 
    {
        $data = [
            'customer_id' => $request->customer_id,
            'date_of_delivery' => $request->date_of_delivery,
            'status' => $request->status,
            'amount_due' => $request->amount_due
        ];

        $createPurchaseOrder = $this->order->create($data);

        if (!$createPurchaseOrder) {
            throw new Exception('Cannot create right now ', 400);
        }

        $items = [];
        foreach ($request->items as $item) {
            $items[] = [
                'sku_id'            => $item['sku_id'],
                'quantity'          => $item['quantity'],
                'price'             => $item['price'],
                'purchase_order_id' => $createPurchaseOrder->id,
                'created_at'        => now(),
                'updated_at'        => now(),
                'user_id'           => 1
            ];
        }

        $this->items::insert($items);

    }

    public function index() 
    {
        $orders = $this->order::paginate(10);    

        return (new PurchaseOrderCollection($orders))->additional([
            'message' => 'Successfully Created',
            'status'  => 200,
        ])
        ->response()
        ->setStatusCode(200);
    }
}


