<?php

namespace App\Services;

use App\Http\Resources\PurchaseOrderCollection;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseItem;
use App\Models\PurchaseOrder;
use Exception;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function __construct(protected PurchaseOrder $order, protected PurchaseItem $items)
    {
        
    }

    public function store($request) 
    {
        DB::beginTransaction();
        try {
            $data = [
                'customer_id'      => $request->customer_id,
                'date_of_delivery' => $request->delivery_date,
                'status'           => $request->status,
                'amount_due'       => $request->amount_due
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

            DB::commit();

            return;

        } catch(Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'status'  => $e->getCode()
            ], $e->getCode());
        }

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

    public function show($id)
    {
        $order = $this->order::with(['purchaseItems'])->find($id);

        return new PurchaseOrderResource($order);

    }
}


