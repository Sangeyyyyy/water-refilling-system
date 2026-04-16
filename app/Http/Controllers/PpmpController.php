<?php

namespace App\Http\Controllers;

use App\Models\Ppmp;
use App\Models\PpmpItem;
use App\Models\Office;
use Illuminate\Http\Request;
use App\Http\Requests\PpmpRequest;
use Illuminate\Support\Facades\DB;

class PpmpController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:web,client']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ppmp::with(['office.division.collegeOffice.campus'])->orderBy('fiscal_year', 'desc');

        // Apply filters
        if ($request->filled('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        if ($request->filled('ppmp_type')) {
            $query->where('ppmp_type', $request->ppmp_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('budget_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('fund_manager', 'like', "%{$search}%")
                  ->orWhereHas('office', function($o) use ($search) {
                      $o->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $ppmps = $query->paginate(15)->withQueryString();
        
        $years = Ppmp::select('fiscal_year')->distinct()->orderBy('fiscal_year', 'desc')->pluck('fiscal_year');

        return view('ppmps.index', compact('ppmps', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $offices = Office::with('division.collegeOffice.campus')->orderBy('name')->get();
        return view('ppmps.create', compact('offices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PpmpRequest $request)
    {
        DB::transaction(function () use ($request) {
            $ppmp = Ppmp::create([
                'office_id' => $request->office_id,
                'budget_code' => $request->budget_code,
                'ppmp_type' => $request->ppmp_type,
                'description' => $request->description,
                'fiscal_year' => $request->fiscal_year,
                'total_budget' => $request->total_budget,
                'remaining_budget' => $request->total_budget,
                'president_approved_date' => $request->president_approved_date,
                'fund_manager' => $request->fund_manager,
                'fund_manager_email' => $request->fund_manager_email,
                'status' => 'approved', // Defaulting to approved for admin creation
            ]);

            foreach ($request->items as $item) {
                PpmpItem::create([
                    'ppmp_id' => $ppmp->id,
                    'description' => $item['description'],
                    'unit' => $item['unit'] ?? null,
                    'mode_of_procurement' => $item['mode_of_procurement'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'q1' => $item['q1'] ?? 0,
                    'q2' => $item['q2'] ?? 0,
                    'q3' => $item['q3'] ?? 0,
                    'q4' => $item['q4'] ?? 0,
                ]);
            }
        });

        return redirect()->route('ppmps.index')->with('success', 'PPMP created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ppmp $ppmp)
    {
        $ppmp->load(['office.division.collegeOffice.campus', 'items']);
        return view('ppmps.show', compact('ppmp'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ppmp $ppmp)
    {
        $ppmp->load('items');
        $offices = Office::with('division.collegeOffice.campus')->orderBy('name')->get();
        return view('ppmps.edit', compact('ppmp', 'offices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PpmpRequest $request, Ppmp $ppmp)
    {
        DB::transaction(function () use ($request, $ppmp) {
            $budgetDifference = $request->total_budget - $ppmp->total_budget;
            $newRemainingBudget = $ppmp->remaining_budget + $budgetDifference;

            $ppmp->update([
                'office_id' => $request->office_id,
                'budget_code' => $request->budget_code,
                'ppmp_type' => $request->ppmp_type,
                'description' => $request->description,
                'fiscal_year' => $request->fiscal_year,
                'total_budget' => $request->total_budget,
                'remaining_budget' => $newRemainingBudget,
                'president_approved_date' => $request->president_approved_date,
                'fund_manager' => $request->fund_manager,
                'fund_manager_email' => $request->fund_manager_email,
                'status' => $request->status,
            ]);

            // Sync items (Delete old ones and re-create for simplicity in this case)
            $ppmp->items()->delete();

            foreach ($request->items as $item) {
                PpmpItem::create([
                    'ppmp_id' => $ppmp->id,
                    'description' => $item['description'],
                    'unit' => $item['unit'] ?? null,
                    'mode_of_procurement' => $item['mode_of_procurement'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'q1' => $item['q1'] ?? 0,
                    'q2' => $item['q2'] ?? 0,
                    'q3' => $item['q3'] ?? 0,
                    'q4' => $item['q4'] ?? 0,
                ]);
            }
        });

        return redirect()->route('ppmps.index')->with('success', 'PPMP updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ppmp $ppmp)
    {
        $ppmp->delete();
        return redirect()->route('ppmps.index')->with('success', 'PPMP deleted successfully.');
    }

    /**
     * Remove the specified resources from storage.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:ppmps,id',
        ]);

        Ppmp::whereIn('id', $request->ids)->delete();

        return redirect()->route('ppmps.index')->with('success', count($request->ids) . ' PPMPs deleted successfully.');
    }
    /**
     * Get unique fund managers and their emails for autocomplete.
     */
    public function getManagers(Request $request)
    {
        $search = $request->query('q');
        if (!$search) return response()->json([]);

        // 1. Search existing PPMPS
        $ppmpManagers = Ppmp::select('fund_manager', 'fund_manager_email')
            ->where(function($q) use ($search) {
                $q->where('fund_manager', 'like', "%{$search}%")
                  ->orWhere('fund_manager_email', 'like', "%{$search}%");
            })
            ->distinct()
            ->get()
            ->map(function($item) {
                return [
                    'fund_manager' => $item->fund_manager,
                    'fund_manager_email' => $item->fund_manager_email,
                    'source' => 'PPMP'
                ];
            });

        // 2. Search Users (Staff/Admin)
        $users = \App\Models\User::select('first_name', 'last_name', 'email')
            ->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(5)
            ->get()
            ->map(function($user) {
                return [
                    'fund_manager' => "{$user->first_name} {$user->last_name}",
                    'fund_manager_email' => $user->email,
                    'source' => 'Staff'
                ];
            });

        // 3. Search Clients
        $clients = \App\Models\Client::select('first_name', 'last_name', 'email')
            ->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->limit(5)
            ->get()
            ->map(function($client) {
                return [
                    'fund_manager' => "{$client->first_name} {$client->last_name}",
                    'fund_manager_email' => $client->email,
                    'source' => 'Client'
                ];
            });

        // Combine and filter unique by email
        $combined = $ppmpManagers->concat($users)->concat($clients)
            ->unique('fund_manager_email')
            ->values()
            ->take(10);

        return response()->json($combined);
    }
}

