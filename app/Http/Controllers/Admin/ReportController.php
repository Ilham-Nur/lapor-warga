<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function data()
    {
        $query = Report::with('type')
            ->select('reports.*')
            ->orderByRaw("
            CASE status
                WHEN 'pending' THEN 1
                WHEN 'verified' THEN 2
                WHEN 'rejected' THEN 3
                ELSE 4
            END
        ")
            ->orderByDesc('occurred_at');

        return DataTables::of($query)
            ->addIndexColumn() 

            ->addColumn('type', function ($row) {
                return $row->type->name ?? '-';
            })

            ->editColumn('occurred_at', function ($row) {
                return $row->occurred_at
                    ? $row->occurred_at->timezone('Asia/Makassar')->format('d M Y H:i')
                    : '-';
            })

            ->addColumn('status_badge', function ($row) {
                return match ($row->status) {
                    'pending'  => '<span class="badge bg-warning">Pending</span>',
                    'verified' => '<span class="badge bg-success">Verified</span>',
                    'rejected' => '<span class="badge bg-danger">Rejected</span>',
                    default    => '<span class="badge bg-secondary">Unknown</span>',
                };
            })

            ->addColumn('action', function ($row) {
                $detail = '<a href="' . route('admin.reports.show', $row->id) . '" 
                        class="btn btn-sm btn-primary me-1">Detail</a>';

                if ($row->status === 'pending') {
                    return $detail . '
                    <form method="POST" action="' . route('admin.reports.approve', $row->id) . '" class="d-inline">
                        ' . csrf_field() . '
                        <button class="btn btn-sm btn-success">Approve</button>
                    </form>
                    <form method="POST" action="' . route('admin.reports.reject', $row->id) . '" class="d-inline ms-1">
                        ' . csrf_field() . '
                        <button class="btn btn-sm btn-danger">Reject</button>
                    </form>
                ';
                }

                return $detail;
            })

            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }


    public function show(Report $report)
    {
        $report->load(['type', 'media']);

        return view('admin.reports.show', compact('report'));
    }

    public function approve(Report $report)
    {
        $report->update([
            'status' => 'verified',
        ]);

        return back()->with('success', 'Laporan berhasil diverifikasi.');
    }

    public function reject(Report $report)
    {
        $report->update([
            'status' => 'rejected',
        ]);

        return back()->with('success', 'Laporan ditolak.');
    }
}
