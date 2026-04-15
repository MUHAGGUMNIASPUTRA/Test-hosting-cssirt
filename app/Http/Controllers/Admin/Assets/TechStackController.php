<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveTechStackRequest;
use App\Models\TechStack;
use App\Models\TechStackCategory;
use App\Services\AttachmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TechStackController extends Controller
{
    public function __construct(private readonly AttachmentService $attachmentService) {}

    public function index(Request $request): Response
    {
        $query = TechStack::with('category')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $techStacks = $query->paginate(15)->withQueryString();
        $categories = TechStackCategory::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Assets/TechStacks/Index', [
            'techStacks' => $techStacks,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Assets/TechStacks/Create', [
            'categories' => TechStackCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(SaveTechStackRequest $request): RedirectResponse
    {
        $attachment = null;
        if ($request->hasFile('logo')) {
            $attachment = $this->attachmentService->storeFile($request->file('logo'), 'public', 'tech-stacks/logos');
        }

        TechStack::create([
            ...$request->validated(),
            'logo_attachment_id' => $attachment?->id,
        ]);

        return redirect()->route('admin.tech-stacks.index')
            ->with('success', 'Tech stack berhasil ditambahkan.');
    }

    public function edit(TechStack $techStack): Response
    {
        return Inertia::render('Admin/Assets/TechStacks/Create', [
            'techStack' => $techStack->load('category', 'logoAttachment'),
            'categories' => TechStackCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(SaveTechStackRequest $request, TechStack $techStack): RedirectResponse
    {
        $attachment = $techStack->logoAttachment;
        if ($request->hasFile('logo')) {
            $this->attachmentService->delete($attachment);
            $attachment = $this->attachmentService->storeFile($request->file('logo'), 'public', 'tech-stacks/logos');
        }

        $techStack->update([
            ...$request->validated(),
            'logo_attachment_id' => $attachment?->id,
        ]);

        return redirect()->route('admin.tech-stacks.index')
            ->with('success', 'Tech stack berhasil diperbarui.');
    }

    public function destroy(TechStack $techStack): RedirectResponse
    {
        $this->attachmentService->delete($techStack->logoAttachment);
        $techStack->delete();

        return redirect()->back()->with('success', 'Tech stack berhasil dihapus.');
    }
}
