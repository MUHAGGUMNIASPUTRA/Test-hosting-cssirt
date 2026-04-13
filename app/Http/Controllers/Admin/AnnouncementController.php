<?php

// filepath: app/Http/Controllers/Admin/AnnouncementController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Announcement\SaveAnnouncementRequest;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Announcement::latest();

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'ilike', '%'.$request->search.'%')
                    ->orWhere('content', 'ilike', '%'.$request->search.'%');
            });
        }

        // Apply level filter
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        // Apply status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'current') {
                $now = Carbon::now();
                $query->where('is_active', true)
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            } elseif ($request->status === 'expired') {
                $query->where('end_date', '<', Carbon::now());
            } elseif ($request->status === 'scheduled') {
                $query->where('start_date', '>', Carbon::now());
            }
        }

        $announcements = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/Announcements/Index', [
            'announcements' => $announcements,
            'levelOptions' => Announcement::getLevelOptions(),
            'filters' => $request->only(['search', 'level', 'status']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveAnnouncementRequest $request): RedirectResponse
    {
        Announcement::create($request->validated());

        return redirect()->back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $announcement->update($request->validated());

        return redirect()->back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
