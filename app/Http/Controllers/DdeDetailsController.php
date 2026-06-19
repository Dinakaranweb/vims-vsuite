<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DdeDetails;

class DdeDetailsController extends Controller
{
    public function index(Request $request)
    {
        // Fetch DDE details with optional date filters
        $query = DdeDetails::query();

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('payment_date', [$request->start_date, $request->end_date]);
        }

        $ddeDetails = $query->get();

        $activeMenu = "postal";
        $activeDropdown = "dde_details";

        // Calculate total amount
        $totalAmount = $ddeDetails->sum('amount');

        return view('frontend.admin.postal.dde_details', compact('ddeDetails', 'totalAmount', 'activeMenu', 'activeDropdown'));
    }
}