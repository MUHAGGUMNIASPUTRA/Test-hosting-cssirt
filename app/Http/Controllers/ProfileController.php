<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
  /**
   * Display the profile page.
   */
  public function __invoke(): Response
  {
    return Inertia::render('Profile/Index');
  }
}
