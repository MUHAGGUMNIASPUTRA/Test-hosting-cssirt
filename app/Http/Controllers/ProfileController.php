<?php
// File: app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;

class ProfileController extends Controller
{
  use HandlesSeoRequests;

  /**
   * Display the profile page.
   */
  public function __invoke()
  {
    return $this->handleSeoRequest('Profile/Index');
  }
}
