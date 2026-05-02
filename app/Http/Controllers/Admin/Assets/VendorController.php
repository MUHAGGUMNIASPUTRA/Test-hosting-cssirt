<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveVendorRequest;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VendorController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Vendor::latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('company_name', 'ilike', '%'.$request->search.'%')
                    ->orWhere('email', 'ilike', '%'.$request->search.'%');
            });
        }

        $vendors = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Assets/Vendors/Index', [
            'vendors' => $vendors,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Assets/Vendors/Create');
    }

    public function store(SaveVendorRequest $request): RedirectResponse
    {
        Vendor::create($request->validated());

        return redirect()->back()
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function edit(Vendor $vendor): Response
    {
        return Inertia::render('Admin/Assets/Vendors/Create', [
            'vendor' => $vendor,
        ]);
    }

    public function update(SaveVendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $vendor->update($request->validated());

        return redirect()->back()
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        $vendor->delete();

        return redirect()->back()->with('success', 'Vendor berhasil dihapus.');
    }
}
