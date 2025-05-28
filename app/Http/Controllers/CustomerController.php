<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomerRequest;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(protected CustomerService $service) 
    {
        
    }

    public function store(CreateCustomerRequest $request) 
    {
        return $this->service->store($request);
    }
    
    public function index(Request $request) 
    {
        return $this->service->index($request);
    }

    public function show($id) 
    {
        return $this->service->show($id);
    }

    public function update($id, CreateCustomerRequest $request) 
    {
        return $this->service->update($id, $request);
    }

    public function destroy($id) 
    {
        return $this->service->destroy($id);
    }
}
