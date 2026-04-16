<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Campus;
use App\Models\CollegeOffice;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        $query = Division::with(['collegeOffice.campus', 'campus'])->withCount('offices');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('college_office_id') && $request->college_office_id != '') {
            $query->where('college_office_id', $request->college_office_id);
        }

        if ($request->has('campus_id') && $request->campus_id != '') {
            $query->where(function($q) use ($request) {
                $q->whereHas('collegeOffice', function($sub) use ($request) {
                    $sub->where('campus_id', $request->campus_id);
                })->orWhere('campus_id', $request->campus_id);
            });
        }

        $divisions = $query->orderBy('name')->paginate(10);
        $campuses = Campus::orderBy('name')->get();
        $collegeOffices = CollegeOffice::orderBy('name')->get();
        
        return view('admin.divisions.index', compact('divisions', 'campuses', 'collegeOffices'));
    }

    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
        $this->middleware('role:admin,superadmin')->except('index', 'show');
    }

    public function create()
    {
        $collegeOffices = CollegeOffice::with('campus')->orderBy('name')->get();
        return view('admin.divisions.create', compact('collegeOffices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'college_office_id' => 'required|exists:college_offices,id',
            'name' => 'required|string|max:255',
        ]);

        Division::create($request->all());

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', 'Division created successfully.');
        }

        return redirect()->route('divisions.index')->with('success', 'Division created successfully.');
    }

    public function edit(Division $division)
    {
        $collegeOffices = CollegeOffice::with('campus')->orderBy('name')->get();
        return view('admin.divisions.edit', compact('division', 'collegeOffices'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'college_office_id' => 'required|exists:college_offices,id',
            'name' => 'required|string|max:255',
        ]);

        $division->update($request->all());

        return redirect()->route('divisions.index')->with('success', 'Division updated successfully.');
    }

    public function destroy(Division $division)
    {
        $division->delete();

        if (request()->filled('redirect_to')) {
            return redirect(request()->redirect_to)->with('success', 'Division deleted successfully.');
        }

        return redirect()->route('divisions.index')->with('success', 'Division deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected'], 400);
        }

        Division::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected divisions deleted successfully.']);
    }
}
