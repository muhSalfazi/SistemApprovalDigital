@extends('layouts.app')

@section('title', 'Submissions')

@section('content')
    <div class="pagetitle">
        <h1>Submissions</h1>
        <nav>
            <ol class="breadcrumb">
                {{-- <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li> --}}
                <li class="breadcrumb-item active">Submissions</li>
            </ol>
        </nav>
    </div>
    <style>
        .pdf-container {
            width: 100%;
            height: 500px;
            /* Atur tinggi maksimum untuk kontainer PDF */
            overflow-y: auto;
            /* Tambahkan scroll jika konten terlalu panjang */
            border: 1px solid #ddd;
            /* Opsional: Tambahkan border */
        }
    </style>
    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">List of Submissions</h5>
                <div class="mb-3">
                    <a href="{{ route('submissions.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-square"></i> Add Submission
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bagian</th>
                                <th>No Transaksi</th>
                                <th>Title</th>
                                <th>Remark</th>
                                <th>Attachment</th>
                                <th>Prepare</th>
                                <th>Date Submission</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($submissions as $submission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $submission->Departement->nama_departement }}</td>
                                    <td>{{ $submission->no_transaksi }}</td>
                                    <td>{{ $submission->title }}</td>
                                    <td>{{ $submission->remark }}</td>
                                    {{-- <td>{{ $submission->user->name }}</td> --}}
                                    <td>
                                        <button class="btn btn-info btn-sm"
                                            onclick="openApprovalModal({{ $submission->id }}, '{{ asset($submission->lampiran_pdf) }}')">
                                            View
                                        </button>
                                    </td>
                                    <td>{{ $submission->user->name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($submission->created_at)->format('d M Y H:i:s') }}</td>
                                    <td>
                                        @if (auth()->user()->role === 'prepared')
                                            <button class="btn btn-primary btn-sm">Edit</button>
                                        @elseif (auth()->user()->role === 'Check 1' && $submission->approvals->where('status', 'approved')->count() === 1)
                                            <button class="btn btn-warning btn-sm">Approve</button>
                                        @elseif (auth()->user()->role === 'Check 2' && $submission->approvals->where('status', 'approved')->count() === 2)
                                            <button class="btn btn-success btn-sm">Finalize</button>
                                        @else
                                            <span class="badge bg-secondary">Tidak Ada Aksi</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($submission->lampiran_pdf)
                                            <a href="{{ route('submissions.download', $submission->id) }}"
                                                class="btn btn-success btn-sm">Download</a>
                                        @else
                                            N/A
                                        @endif
                                        {{-- <form action="#" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form> --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="table-responsive">
                    </div>
                </div>

                <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg"> <!-- Ganti ke modal ukuran sedang -->
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="approvalModalLabel">Approval Submission</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Kontainer untuk iframe PDF -->
                                <div class="pdf-container rounded shadow-sm">
                                    <iframe id="pdfViewer" src="" width="100%" height="500px" frameborder="0"
                                        class="rounded"></iframe>
                                </div>
                                <div class="mt-3">
                                    <label>Status:</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="approvalStatus"
                                            id="approveStatus" value="approved">
                                        <label class="form-check-label" for="approveStatus">YES</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="approvalStatus"
                                            id="rejectStatus" value="rejected">
                                        <label class="form-check-label" for="rejectStatus">NO</label>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label for="remark">Remark (Required if NO):</label>
                                    <textarea id="remark" class="form-control" rows="3" placeholder="Enter your remark"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="submitApproval()">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .pdf-container {
                        height: 500px;
                        /* Batasi tinggi PDF */
                        overflow-y: auto;
                        /* Tambahkan scroll jika diperlukan */
                        border: 1px solid #ddd;
                        background-color: #f8f9fa;
                        padding: 5px;
                    }

                    iframe {
                        border: none;
                    }

                    .modal-content {
                        border-radius: 10px;
                        overflow: hidden;
                    }
                </style>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    function openApprovalModal(submissionId, pdfPath) {
                        const pdfViewer = document.getElementById('pdfViewer');
                        pdfViewer.src = pdfPath;
                        pdfViewer.setAttribute('data-id', submissionId);

                        // Reset input modal
                        document.getElementById('approveStatus').checked = false;
                        document.getElementById('rejectStatus').checked = false;
                        document.getElementById('remark').value = '';

                        const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                        modal.show();
                    }


                    document.addEventListener('DOMContentLoaded', function() {
                        // Tambahkan event listener untuk status YES dan NO
                        document.getElementById('approveStatus').addEventListener('change', function() {
                            if (this.checked) {
                                Swal.fire({
                                    title: 'Konfirmasi Persetujuan',
                                    text: 'Apakah Anda sudah melihat dokumen ini dengan teliti?',
                                    icon: 'info',
                                    confirmButtonText: 'Ya, Saya Sudah',
                                    cancelButtonText: 'Batal',
                                    showCancelButton: true
                                }).then((result) => {
                                    if (!result.isConfirmed) {
                                        this.checked = false; // Batalkan pilihan jika user membatalkan
                                    }
                                });
                            }
                        });

                        document.getElementById('rejectStatus').addEventListener('change', function() {
                            if (this.checked) {
                                Swal.fire({
                                    title: 'Konfirmasi Penolakan',
                                    text: 'Pastikan Anda memiliki alasan yang valid untuk menolak. Apakah Anda sudah melihat dokumen ini?',
                                    icon: 'warning',
                                    confirmButtonText: 'Ya, Saya Sudah',
                                    cancelButtonText: 'Batal',
                                    showCancelButton: true
                                }).then((result) => {
                                    if (!result.isConfirmed) {
                                        this.checked = false; // Batalkan pilihan jika user membatalkan
                                    }
                                });
                            }
                        });
                    });

                    function submitApproval() {
                        const status = document.querySelector('input[name="approvalStatus"]:checked');
                        const remark = document.getElementById('remark').value;
                        const submissionId = document.getElementById('pdfViewer').getAttribute('data-id');

                        if (!status) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Silakan pilih status (YA atau TIDAK).',
                                width: '400px',
                            });
                            return;
                        }

                        if (status.value === 'rejected' && !remark.trim()) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Alasan wajib diisi jika status TIDAK dipilih.',
                                width: '400px',
                            });
                            return;
                        }

                        const submissionData = {
                            id_submission: submissionId,
                            status: status.value,
                            remark: remark,
                            _token: '{{ csrf_token() }}',
                        };

                        fetch('{{ route('submissions.approval') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(submissionData),
                            })
                            .then((response) => {
                                if (!response.ok) {
                                    return response.json().then((err) => {
                                        throw new Error(err.error || 'Terjadi kesalahan saat mengirim data.');
                                    });
                                }
                                return response.json();
                            })
                            .then((data) => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: data.success,
                                    width: '400px',
                                }).then(() => {
                                    location.reload(); // Refresh halaman
                                });
                            })
                            .catch((error) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan',
                                    text: error.message,
                                    width: '400px',
                                });
                            });
                    }
                </script>


    </section>
@endsection