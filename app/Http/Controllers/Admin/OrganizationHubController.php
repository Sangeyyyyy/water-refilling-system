<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\CollegeOffice;
use App\Models\Division;
use App\Models\Office;
use Illuminate\Http\Request;

class OrganizationHubController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $campuses = Campus::with(['collegeOffices.divisions.offices'])->orderBy('name')->get();
        return view('admin.org_hub.index', compact('campuses'));
    }

    /**
     * Get children for a specific node (for AJAX expansion if we go that route)
     * For now, we load all to keep the tree simple and fast.
     */
    public function getNodes()
    {
        $campuses = Campus::with(['collegeOffices.divisions.offices'])->orderBy('name')->get();
        return response()->json($campuses);
    }
}
