<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentArea\SaveDocumentAreaRequest;
use App\Models\DocumentArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DocumentAreaController extends Controller
{
    public function index(Request $request): Response
    {
        $query = DocumentArea::withCount('documents');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        return Inertia::render('Admin/DocumentAreas/Index', [
            'documentAreas' => $query->orderBy('name')->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/DocumentAreas/Create');
    }

    public function store(SaveDocumentAreaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        DocumentArea::create($data);

        return redirect()
            ->route('admin.document-areas.index')
            ->with('success', 'Area dokumen berhasil dibuat.');
    }

    public function edit(DocumentArea $documentArea): Response
    {
        return Inertia::render('Admin/DocumentAreas/Create', [
            'documentArea' => $documentArea->loadCount('documents'),
        ]);
    }

    public function update(SaveDocumentAreaRequest $request, DocumentArea $documentArea): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        $documentArea->update($data);

        return redirect()
            ->route('admin.document-areas.index')
            ->with('success', 'Area dokumen berhasil diperbarui.');
    }

    public function destroy(DocumentArea $documentArea)
    {
        if ($documentArea->documents()->count() > 0) {
            return back()->with('error', [
                'title' => 'Gagal Menghapus',
                'message' => 'Area dokumen tidak dapat dihapus karena masih digunakan dalam '.$documentArea->documents()->count().' dokumen.',
                'icon' => 'error',
            ]);
        }

        try {
            $documentArea->delete();

            return back()->with('success', [
                'title' => 'Berhasil',
                'message' => 'Area dokumen berhasil dihapus.',
                'icon' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal',
                'message' => 'Gagal menghapus area dokumen.',
                'icon' => 'error',
            ]);
        }
    }
}
