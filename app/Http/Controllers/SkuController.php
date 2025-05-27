<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSkuRequest;
use App\Services\SkuService;

class SkuController extends Controller
{
    public function __construct(protected SkuService $service) 
    {
        
    }

    public function store(CreateSkuRequest $request) 
    {
        return $this->service->store($request);
    }
    
    public function index() 
    {
        return $this->service->index();
    }

    public function show($id) 
    {
        return $this->service->show($id);
    }

    public function update($id, CreateSkuRequest $request) 
    {
        return $this->service->update($id, $request);
    }

    public function destroy($id) 
    {
        return $this->service->destroy($id);
    }
}
