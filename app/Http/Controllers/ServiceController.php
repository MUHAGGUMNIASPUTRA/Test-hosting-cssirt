<?php
// File: app/Http/Controllers/ServiceController.php

namespace App\Http\Controllers;

use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
  /**
   * Display a listing of the services.
   */
  public function index(): Response
  {
    return Inertia::render('Services/Index', [
      'services' => Service::where('is_active', true)->get(),
    ]);
  }
}
