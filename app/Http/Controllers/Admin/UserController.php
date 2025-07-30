<?php
// filepath: app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request): Response
  {
    $query = User::latest();

    // Apply search filter
    if ($request->filled('search')) {
      $query->where(function ($q) use ($request) {
        $q->where('name', 'ilike', '%' . $request->search . '%')
          ->orWhere('email', 'ilike', '%' . $request->search . '%');
      });
    }

    // Apply role filter
    if ($request->filled('role')) {
      $query->where('role', $request->role);
    }

    $users = $query->orderBy('name')->paginate(10)->withQueryString();

    return Inertia::render('Admin/Users/Index', [
      'users' => $users,
      'roleOptions' => User::getRoleOptions(),
      'filters' => $request->only(['search', 'role']),
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:'.User::class,
      'password' => ['required', 'confirmed', Rules\Password::min(8)],
      'role' => 'required|in:admin,staff,user',
    ]);

    User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'role' => $request->role,
    ]);

    return redirect()->back()->with('success', 'Pengguna baru berhasil ditambahkan.');
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, User $user)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
      'current_password' => 'required|string',
      'password' => ['nullable', 'confirmed', Rules\Password::min(8)],
      'role' => 'required|in:admin,staff,user',
    ]);

    // Verify the current password for the user being edited
    if (!Hash::check($request->current_password, $user->password)) {
      return back()->withErrors(['current_password' => 'Password saat ini salah.']);
    }

    $updateData = [
      'name' => $request->name,
      'email' => $request->email,
      'role' => $request->role,
    ];

    // Only update password if new password is provided
    if ($request->filled('password')) {
      $updateData['password'] = Hash::make($request->password);
    }

    $user->update($updateData);

    return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(User $user)
  {
    // Prevent admin from deleting their own account
    if ($user->id === auth()->id()) {
      return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
    }

    $user->delete();
    return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
  }
}
