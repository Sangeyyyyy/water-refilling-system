<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Ppmp;
use App\Models\Office;

class OfficePpmpController extends Controller
{
    public function getBalance(Office $office, Request $request)
    {
        $query = Ppmp::where('office_id', $office->id)
            ->where('status', 'approved');

        if ($request->has('ppmp_id')) {
            $query->where('id', $request->ppmp_id);
        } elseif ($request->has('budget_code')) {
            $query->where('budget_code', $request->budget_code);
        }

        $ppmp = $query->orderBy('fiscal_year', 'desc')->first();

        if (!$ppmp) {
            return response()->json([
                'has_ppmp' => false,
                'remaining_budget' => 0,
                'fiscal_year' => $request->budget_code ? 'N/A' : date('Y'),
                'message' => 'No approved PPMP found.'
            ]);
        }

        return response()->json([
            'has_ppmp' => true,
            'remaining_budget' => $ppmp->remaining_budget,
            'fiscal_year' => $ppmp->fiscal_year,
            'budget_code' => $ppmp->budget_code,
            'status' => $ppmp->status
        ]);
    }

    public function getBudgetCodes(Office $office)
    {
        $ppmps = Ppmp::where('office_id', $office->id)
            ->where('status', 'approved')
            ->orderBy('fiscal_year', 'desc')
            ->get(['id', 'budget_code', 'ppmp_type'])
            ->map(function($ppmp) {
                return [
                    'id' => $ppmp->id,
                    'budget_code' => $ppmp->budget_code,
                    'ppmp_type' => $ppmp->ppmp_type,
                    'display' => "{$ppmp->budget_code} ({$ppmp->ppmp_type})"
                ];
            });

        return response()->json([
            'budget_codes' => $ppmps
        ]);
    }
}
