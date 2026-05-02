<?php

// Tujuan: CRUD daftar subdomain (inline dialog, tanpa halaman create/edit terpisah)
// Caller: routes/web.php admin group
// Side Effects: DB write

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveSubdomainRequest;
use App\Models\Subdomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubdomainController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Subdomain::query();

        if ($request->filled('search')) {
            $query->where('subdomain', 'ilike', '%'.$request->search.'%');
        }

        $subdomains = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Assets/Subdomains/Index', [
            'subdomains' => $subdomains,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(SaveSubdomainRequest $request): RedirectResponse
    {
        Subdomain::create($request->validated());

        return redirect()->back()->with('success', 'Subdomain berhasil ditambahkan.');
    }

    public function update(SaveSubdomainRequest $request, Subdomain $subdomain): RedirectResponse
    {
        $subdomain->update($request->validated());

        return redirect()->back()->with('success', 'Subdomain berhasil diperbarui.');
    }

    public function destroy(Subdomain $subdomain): RedirectResponse
    {
        $subdomain->delete();

        return redirect()->back()->with('success', 'Subdomain berhasil dihapus.');
    }
}
