<?php

namespace App\Services;

use App\Http\Resources\SkuCollection;
use App\Http\Resources\SkuResource;
use App\Models\Sku;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class SkuService 
{
    public function __construct(protected Sku $sku)
    {
        
    }   
    
    public function store($request) 
    {   
        $loggedUser = Auth::user();

        $request['created_by'] = $loggedUser->name;
        $request['user_id'] = $loggedUser->id;

        $sku = $this->sku->create($request->all());

        return (new SkuResource($sku))->additional([
                'message' => 'Successfully Created',
                'status'  => 200,
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function index()
    {
        $skus = $this->sku::paginate(10);

        return (new SkuCollection($skus))->additional([
                'message' => 'Ok'
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function show($id)
    {
        $sku = $this->fetchSpecificSku($id);

        if (!$sku) {
            throw new ModelNotFoundException('Sku not found');
        }
    
        return (new SkuResource($sku))->additional([
                'message' => 'Ok',
                'status'  => 200,
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function update($id, $request)
    {
        $sku = $this->fetchSpecificSku($id);

        $sku->update($request->all());

        return (new SkuResource($sku))->additional([
                'message' => 'Successfully Updated',
                'status'  => 200,
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function destroy($id)
    {
        $sku = $this->fetchSpecificSku($id);

        $sku->update([
            'is_active' => false
        ]);

        return response()->json([
            'message' => 'Successfully Deleted',
            'status'  => 200,
        ], 200);
    }

    private function fetchSpecificSku($id) 
    {
        $sku = $this->sku::find($id);
        
        if (!$sku) {
            throw new ModelNotFoundException('Sku not found');
        }

        return $sku;
    }
}