<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,superadmin')->except('index', 'show');
    }

    public function index(Request $request)
    {
        $query = Campus::withCount('divisions');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $campuses = $query->paginate(10);
        return view('admin.campuses.index', compact('campuses'));
    }

    public function create()
    {
        return view('admin.campuses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:campuses',
        ]);

        Campus::create($request->all());

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', 'Campus created successfully.');
        }

        return redirect()->route('campuses.index')->with('success', 'Campus created successfully.');
    }

    public function edit(Campus $campus)
    {
        return view('admin.campuses.edit', compact('campus'));
    }

    public function update(Request $request, Campus $campus)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:campuses,name,' . $campus->id,
        ]);

        $campus->update($request->all());

        return redirect()->route('campuses.index')->with('success', 'Campus updated successfully.');
    }

    public function destroy(Campus $campus)
    {
        $campus->delete();

        if (request()->filled('redirect_to')) {
            return redirect(request()->redirect_to)->with('success', 'Campus deleted successfully.');
        }

        return redirect()->route('campuses.index')->with('success', 'Campus deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected'], 400);
        }

        Campus::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected campuses deleted successfully.']);
    }
}
