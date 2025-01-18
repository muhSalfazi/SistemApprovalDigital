@extends('layouts.app')

@section('title', 'Approval List')

@section('content')
    <div class="pagetitle">
        <h1>Approval List</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Approvals</li>
            </ol>
        </nav>
    </div>
    <style>
        .badge {
            font-size: 0.875rem;
            padding: 5px 10px;
            border-radius: 12px;
        }

        .bg-info {
            color: #fff;
            background-color: #17a2b8;
        }

        .bg-success {
            color: #fff;
            background-color: #28a745;
        }

        .bg-danger {
            color: #fff;
            background-color: #dc3545;
        }

        .bg-secondary {
            color: #fff;
            background-color: #6c757d;
        }
    </style>


    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">List of Approvals</h5>
                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No. Doc</th>
                                <th scope="col" class="text-center">Nama Document</th>
                                <th scope="col" class="text-center">Prepare</th>
                                <th scope="col" class="text-center">Check-1</th>
                                <th scope="col" class="text-center">Check-2</th>
                                <th scope="col" class="text-center">Approved</th>
                                <th scope="col" class="text-center">Remark</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($submissions as $submission)
                                <tr>
                                    <td scope="col" class="text-center">{{ $submission->no_transaksi }}</td>
                                    <td scope="col" class="text-center">{{ $submission->title }}</td>

                                    <!-- Prepare -->
                                    <td scope="col" class="text-center">
                                        {{ $submission->user->name ?? 'Unknown' }}<br>
                                        {{ $submission->created_at->format('d M Y H:i:s') }}<br>
                                        <span class="badge bg-info">Submitted</span>
                                    </td>

                                    <!-- Check-1 -->
                                    <td scope="col" class="text-center">
                                        @php
                                            $check1Approval = $submission->approvals->first(function ($approval) {
                                                return $approval->user->roles->contains('name', 'Check1');
                                            });
                                        @endphp
                                        @if ($check1Approval)
                                            {{ $check1Approval->user->name ?? 'Unknown' }}<br>
                                            {{ $check1Approval->approved_date ? \Carbon\Carbon::parse($check1Approval->approved_date)->format('d M Y H:i:s') : 'N/A' }}
                                            <span
                                                class="badge bg-{{ $check1Approval->status === 'approved' ? 'success' : 'danger' }}">
                                                {{ ucfirst($check1Approval->status) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>

                                    <!-- Check-2 -->
                                    <td scope="col" class="text-center">
                                        @php
                                            $check2Approval = $submission->approvals->first(function ($approval) {
                                                return $approval->user->roles->contains('name', 'Check2');
                                            });
                                        @endphp
                                        @if ($check2Approval)
                                            {{ $check2Approval->user->name ?? 'Unknown' }}<br>
                                            {{ $check2Approval->approved_date ? \Carbon\Carbon::parse($check2Approval->approved_date)->format('d M Y H:i:s') : 'N/A' }}
                                            <span
                                                class="badge bg-{{ $check2Approval->status === 'approved' ? 'success' : 'danger' }}">
                                                {{ ucfirst($check2Approval->status) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>

                                    <!-- Approved -->
                                    <td scope="col" class="text-center">
                                        @php
                                            $approvedApproval = $submission->approvals->first(function ($approval) {
                                                return $approval->user->roles->contains('name', 'approved');
                                            });
                                        @endphp
                                        @if ($approvedApproval)
                                            {{ $approvedApproval->user->name ?? 'Unknown' }}<br>
                                            {{ $approvedApproval->approved_date ? \Carbon\Carbon::parse($approvedApproval->approved_date)->format('d M Y H:i:s') : 'N/A' }}
                                            <span
                                                class="badge bg-{{ $approvedApproval->status === 'approved' ? 'success' : 'danger' }}">
                                                {{ ucfirst($approvedApproval->status) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td scope="col" class="text-center">
                                        {{ $submission->approvals->last()?->remark ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </section>
@endsection
