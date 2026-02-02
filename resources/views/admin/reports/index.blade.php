@extends('admin.layouts.app')

@section('title', 'Laporan Warga')

@section('content')
    <h4 class="mb-3">📄 Laporan Warga</h4>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered" id="reports-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jenis</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#reports-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.reports.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'type.name'
                    },
                    {
                        data: 'occurred_at'
                    },
                    {
                        data: 'status_badge',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
    </script>
@endpush
