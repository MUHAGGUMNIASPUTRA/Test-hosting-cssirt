<?php

// File: app/Http/Controllers/ServiceController.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Models\Service;

class ServiceController extends Controller
{
    use HandlesSeoRequests;

    /**
     * Display a listing of the services.
     */
    public function index()
    {
        return $this->handleSeoRequest('Services/Index', [
            'services' => Service::where('is_active', true)->get(),
        ]);
    }
}
