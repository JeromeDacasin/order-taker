<?php

namespace App\Services;

use App\Http\Resources\CustomerCollection;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class CustomerService 
{
    public function __construct(protected Customer $customer)
    {
        
    }   
    
    public function store($request) 
    {   
        $loggedUser = Auth::user();

        $request['full_name'] = $request->first_name . ' ' . $request->last_name;
        $request['created_by'] = $loggedUser->name;
        $request['user_id'] = $loggedUser->id;

        $customer = $this->customer->create($request->all());

        return (new CustomerResource($customer))->additional([
                'message' => 'Successfully Created',
                'status'  => 200,
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function index()
    {
        $customers = $this->customer::active()->paginate(10);

        return (new CustomerCollection($customers))->additional([
                'message' => 'Ok'
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function show($id)
    {
        $customer = $this->fetchSpecificCustomer($id);

        if (!$customer) {
            throw new ModelNotFoundException('Customer not found');
        }
    
        return (new CustomerResource($customer))->additional([
                'message' => 'Ok',
                'status'  => 200,
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function update($id, $request)
    {
        $customer = $this->fetchSpecificCustomer($id);

        $customer->update($request->all());

        return (new CustomerResource($customer))->additional([
                'message' => 'Successfully Updated',
                'status'  => 200,
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function destroy($id)
    {
        $customer = $this->fetchSpecificCustomer($id);

        $customer->update([
            'is_active' => false
        ]);

        return response()->json([
            'message' => 'Successfully Deleted',
            'status'  => 200,
        ], 200);
    }

    private function fetchSpecificCustomer($id) 
    {
        $customer = $this->customer::active()->find($id);
        
        if (!$customer) {
            throw new ModelNotFoundException('Customer not found');
        }

        return $customer;
    }
}