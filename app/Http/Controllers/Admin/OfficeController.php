<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Models\Division;
use App\Models\Campus;
use App\Models\Ppmp;
use App\Models\PpmpItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfficeController extends Controller
{
    public function index(Request $request)
    {
        $query = Office::with(['division.collegeOffice.campus', 'latestApprovedPpmp']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('campus_id') && $request->campus_id != '') {
            $query->whereHas('division.collegeOffice', function($q) use ($request) {
                $q->where('campus_id', $request->campus_id);
            });
        }

        if ($request->has('division_id') && $request->division_id != '') {
            $query->where('division_id', $request->division_id);
        }

        $offices = $query->paginate(10);
        $campuses = Campus::orderBy('name')->get();
        $divisions = Division::with(['collegeOffice.campus'])->orderBy('name')->get();

        return view('admin.offices.index', compact('offices', 'campuses', 'divisions'));
    }

    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
        $this->middleware('role:admin,superadmin')->except('index', 'show');
    }

    public function create()
    {
        $divisions = Division::with(['collegeOffice.campus'])->orderBy('name')->get();
        return view('admin.offices.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255',
            'initial_budget' => 'nullable|numeric|min:0',
            'fiscal_year' => 'nullable|required_with:initial_budget|integer|min:2020',
        ]);

        DB::transaction(function() use ($request) {
            $office = Office::create([
                'division_id' => $request->division_id,
                'name' => $request->name,
            ]);

            if ($request->filled('initial_budget') && $request->initial_budget > 0) {
                $ppmp = Ppmp::create([
                    'office_id' => $office->id,
                    'fiscal_year' => $request->fiscal_year,
                    'total_budget' => $request->initial_budget,
                    'remaining_budget' => $request->initial_budget,
                    'status' => 'approved',
                ]);

                PpmpItem::create([
                    'ppmp_id' => $ppmp->id,
                    'description' => 'Initial Water Refill Allocation',
                    'quantity' => ceil($request->initial_budget / \App\Models\Setting::getUnitPrice()), // Approximation based on price
                    'unit_price' => \App\Models\Setting::getUnitPrice(),
                    'total_price' => $request->initial_budget,
                ]);
            }
        });

        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)->with('success', 'Unit created successfully.');
        }

        return redirect()->route('offices.index')->with('success', 'Unit created successfully.');
    }

    public function edit(Office $office)
    {
        $divisions = Division::with(['collegeOffice.campus'])->orderBy('name')->get();
        return view('admin.offices.edit', compact('office', 'divisions'));
    }

    public function update(Request $request, Office $office)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name' => 'required|string|max:255',
            'initial_budget' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function() use ($request, $office) {
            $office->update([
                'division_id' => $request->division_id,
                'name' => $request->name,
            ]);

            if ($request->filled('initial_budget')) {
                $fiscalYear = date('Y');
                $ppmp = Ppmp::where('office_id', $office->id)
                    ->where('fiscal_year', $fiscalYear)
                    ->where('status', 'approved')
                    ->first();

                if ($ppmp) {
                    // Adjust budget logic: 
                    // New budget - Old budget = change
                    $diff = $request->initial_budget - $ppmp->total_budget;
                    $ppmp->total_budget = $request->initial_budget;
                    $ppmp->remaining_budget += $diff;
                    $ppmp->save();
                    
                    // Update the generic item price too
                    $item = $ppmp->items()->first();
                    if ($item) {
                        $item->update([
                            'total_price' => $request->initial_budget,
                            'quantity' => ceil($request->initial_budget / \App\Models\Setting::getUnitPrice()),
                        ]);
                    }
                } else if ($request->initial_budget > 0) {
                    // Create new
                    $ppmp = Ppmp::create([
                        'office_id' => $office->id,
                        'fiscal_year' => $fiscalYear,
                        'total_budget' => $request->initial_budget,
                        'remaining_budget' => $request->initial_budget,
                        'status' => 'approved',
                    ]);

                    PpmpItem::create([
                        'ppmp_id' => $ppmp->id,
                        'description' => 'Initial Water Refill Allocation',
                        'quantity' => ceil($request->initial_budget / \App\Models\Setting::getUnitPrice()),
                        'unit_price' => \App\Models\Setting::getUnitPrice(),
                        'total_price' => $request->initial_budget,
                    ]);
                }
            }
        });

        return redirect()->route('offices.index')->with('success', 'Unit updated successfully.');
    }

    public function destroy(Office $office)
    {
        $office->delete();

        if (request()->filled('redirect_to')) {
            return redirect(request()->redirect_to)->with('success', 'Unit deleted successfully.');
        }

        return redirect()->route('offices.index')->with('success', 'Unit deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No items selected'], 400);
        }

        Office::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Selected units deleted successfully.']);
    }
}
