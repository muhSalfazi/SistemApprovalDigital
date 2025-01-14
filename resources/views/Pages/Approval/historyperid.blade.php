@extends('layouts.app')

@section('title', 'Approval List')

@section('content')
    <div class="pagetitle">
        <h1>Approval List</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('submissions.index') }}">Submissions</a></li>
                <li class="breadcrumb-item active">Approvals</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <!-- Informasi Bagian (Departemen) -->
                <h5 class="card-title">List of Approvals</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered datatable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bagian</th>
                                <th>No Transaksi</th>
                                <th>Submission Title</th>
                                <th>Remark Submission</th>
                                <th>Prepared By</th>
                                <th>Date Submission</th>
                                <th>Approved Date</th>
                                <th>Status</th>
                                <th>Approved By</th>
                                <th>Remark Approval</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($approvals as $approval)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $approval->submission->departement->nama_departement ?? 'Unknown' }}</td>
                                    <td>{{ $approval->submission->no_transaksi ?? 'Unknown' }}</td>
                                    <td>{{ $approval->submission->title ?? 'Unknown' }}</td>
                                    <td>{{ $approval->submission->remark ?? 'Unknown' }}</td>
                                    <td>{{ $approval->submission->user->name ?? 'Unknown' }}</td>
                                    <td>
                                        {{ $approval->submission->created_at ? \Carbon\Carbon::parse($approval->submission->created_at)->format('d M Y H:i:s') : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $approval->approved_date ? \Carbon\Carbon::parse($approval->approved_date)->format('d M Y H:i:s') : 'N/A' }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $approval->status === 'approved' ? 'success' : 'danger' }}">
                                            {{ ucfirst($approval->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $approval->user->name }} | {{ $approval->user->role->name }}</td>
                                    <td>{{ $approval->remark ?? '-' }}</td>
                                </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                    <!-- Tombol Kembali -->
                    <div class="mb-3">
                        <a href="{{ route('submissions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Back to Submissions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
