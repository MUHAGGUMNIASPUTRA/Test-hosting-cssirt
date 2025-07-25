<?php
// File: app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
  public function index(): Response
  {
    // You can fetch summary data here later
    // For now, just render the page
    return Inertia::render('Admin/Dashboard');
  }
}
