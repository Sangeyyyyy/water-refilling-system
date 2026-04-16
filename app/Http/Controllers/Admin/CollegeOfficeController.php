<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CollegeOffice;
use App\Models\Campus;
use Illuminate\Http\Request;

class CollegeOfficeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
        $this->middleware('role:admin,superadmin')->except('index', 'show');
    }

    public function index(Request $request)
    {
        $query = CollegeOffice::with('campus');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhereHas('campus', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        if ($request->has('campus_id') && $request->campus_id != '') {
            $query->where('campus_id', $request->campus_id);
        }

        $collegeOffices = $query->orderBy('name')->paginate(10);
        $campuses = Campus::orderBy('name')->get();

        return view('admin.college_offices.index', compact('collegeOffices', 'campuses'));
    }

    public function create()
    {
        $campuses = Campus::orderBy('name')->get();
        return view('admin.college_offices.create', compact('campuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'name' => 'required|string|max:255',
        ]);

        CollegeOffice::create($request->all());

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', 'Office created successfully.');
        }

        return redirect()->route('college-offices.index')->with('success', 'Office created successfully.');
    }

    public function edit(CollegeOffice $collegeOffice)
    {
        $campuses = Campus::orderBy('name')->get();
        return view('admin.college_offices.edit', compact('collegeOffice', 'campuses'));
    }

    public function update(Request $request, CollegeOffice $collegeOffice)
    {
        $request->validate([
            'campus_id' => 'required|exists:campuses,id',
            'name' => 'required|string|max:255',
        ]);

        $collegeOffice->update($request->all());

        return redirect()->route('college-offices.index')->with('success', 'Office updated successfully.');
    }

    public function destroy(CollegeOffice $collegeOffice)
    {
        $collegeOffice->delete();

        if (request()->filled('redirect_to')) {
            return redirect(request()->redirect_to)->with('success', 'Office deleted successfully.');
        }

        return redirect()->route('college-offices.index')->with('success', 'Office deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected'], 400);
        }

        CollegeOffice::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected offices deleted successfully.']);
    }
}
