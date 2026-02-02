<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use App\Models\ReportType;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔹 COUNT STATUS
        $stats = [
            'total'     => Report::count(),
            'pending'   => Report::where('status', 'pending')->count(),
            'verified'  => Report::where('status', 'verified')->count(),
            'rejected'  => Report::where('status', 'rejected')->count(),
            'today'     => Report::whereDate('created_at', now())->count(),
        ];

        // 🔹 TOP JENIS KEJADIAN
        $topTypes = Report::select('report_type_id', DB::raw('COUNT(*) as total'))
            ->with('type:id,name')
            ->groupBy('report_type_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 🔹 LAPORAN TERBARU (prioritas pending)
        $latestReports = Report::with('type')
            ->orderByRaw("
                CASE status
                    WHEN 'pending' THEN 1
                    WHEN 'verified' THEN 2
                    ELSE 3
                END
            ")
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'topTypes',
            'latestReports'
        ));
    }
}
