<?php

// Tujuan: CRUD daftar IP address (inline dialog, tanpa halaman create/edit terpisah)
// Caller: routes/web.php admin group
// Side Effects: DB write

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveIpAddressRequest;
use App\Models\IpAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IpAddressController extends Controller
{
    public function index(Request $request): Response
    {
        $query = IpAddress::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('private_ip', 'ilike', '%'.$request->search.'%')
                    ->orWhere('public_ip', 'ilike', '%'.$request->search.'%');
            });
        }

        $ipAddresses = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Assets/IpAddresses/Index', [
            'ipAddresses' => $ipAddresses,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(SaveIpAddressRequest $request): RedirectResponse
    {
        IpAddress::create($request->validated());

        return redirect()->back()->with('success', 'IP address berhasil ditambahkan.');
    }

    public function update(SaveIpAddressRequest $request, IpAddress $ipAddress): RedirectResponse
    {
        $ipAddress->update($request->validated());

        return redirect()->back()->with('success', 'IP address berhasil diperbarui.');
    }

    public function destroy(IpAddress $ipAddress): RedirectResponse
    {
        $ipAddress->delete();

        return redirect()->back()->with('success', 'IP address berhasil dihapus.');
    }
}
