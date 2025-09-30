<?php

namespace App\Http\Controllers;

use App\Models\BvsCall;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CallReportController extends Controller
{
    /**
     * Page 1: Call Dashboard – Overview by Month (Current Year)
     */
    public function dashboard()
    {
        $currentYear = '2018';

        // Get monthly data for current year (for chart)
        $monthlySummary = BvsCall::getMonthlySummary($currentYear);

        // Get all-time monthly data (for table)
        $allTimeData = BvsCall::getAllTimeMonthlySummary();

        // Prepare chart data
        $chartData = $this->prepareChartData($monthlySummary);

        return view('calls.dashboard', [
            'currentYear' => $currentYear,
            'monthlySummary' => $monthlySummary,
            'allTimeData' => $allTimeData,
            'chartData' => $chartData
        ]);
    }

    /**
     * Page 2: Monthly Summary by Extension
     */
    public function monthlySummary(Request $request, $yearMonth)
    {
        // Validate year-month format
        if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            abort(404);
        }

        $extensionSummary = BvsCall::getExtensionSummary($yearMonth);

        // Get total stats for the month
        $totalCalls = $extensionSummary->sum('num_calls');
        $totalCost = $extensionSummary->sum('total_cost');

        // Format display month
        $date = Carbon::createFromFormat('Y-m', $yearMonth);
        $displayMonth = $date->format('Y-M');

        return view('calls.summary', [
            'yearMonth' => $yearMonth,
            'displayMonth' => $displayMonth,
            'extensionSummary' => $extensionSummary,
            'totalCalls' => $totalCalls,
            'totalCost' => $totalCost
        ]);
    }

    /**
     * Page 3: Extension Detail View (Monthly Call List)
     */
    public function extensionDetails(Request $request, $yearMonth, $extension)
    {
        // Validate inputs
        if (!preg_match('/^\d{4}-\d{2}$/', $yearMonth)) {
            abort(404);
        }

        $callDetails = BvsCall::getCallDetails($yearMonth, $extension);

        // Get extension name from the first result in callDetails
        $extensionName = "Extension $extension";
        if ($callDetails->count() > 0) {
            $extensionName = $callDetails->first()->CallFrom;
        }

        // Format display month
        $date = Carbon::createFromFormat('Y-m', $yearMonth);
        $displayMonth = $date->format('Y-M');

        // Calculate totals from the paginated collection
        $totalCalls = $callDetails->total();

        // Get the actual total cost (not just from paginated results)
        $totalCostResult = BvsCall::selectRaw('SUM(Cost) as total')
            ->whereRaw("DATE_FORMAT(CallTime, '%Y-%m') = ?", [$yearMonth])
            ->whereRaw("CallFrom LIKE ?", ["%($extension)%"])
            ->first();

        $totalCost = $totalCostResult ? $totalCostResult->total : 0;

        return view('calls.details', [
            'yearMonth' => $yearMonth,
            'displayMonth' => $displayMonth,
            'extension' => $extension,
            'extensionName' => $extensionName,
            'callDetails' => $callDetails,
            'totalCalls' => $totalCalls,
            'totalCost' => $totalCost
        ]);
    }

    /**
     * Preparing data for ECharts
     */
    private function prepareChartData($monthlySummary)
    {
        $months = [];
        $numCalls = [];
        $totalCost = [];

        // Create array for all months Jan-Dec
        $allMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                      'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($allMonths as $index => $month) {
            $monthNum = $index + 1;

            // Try to find the month data - check the actual property name
            $found = $monthlySummary->first(function($item) use ($monthNum) {
                return (int)$item->month_num === $monthNum;
            });

            $months[] = $month;
            $numCalls[] = $found ? (int)$found->num_calls : 0;
            $totalCost[] = $found ? round((float)$found->total_cost, 2) : 0;
        }

        return [
            'months' => $months,
            'numCalls' => $numCalls,
            'totalCost' => $totalCost
        ];
    }
}
