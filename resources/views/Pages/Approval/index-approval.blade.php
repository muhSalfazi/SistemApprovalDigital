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
    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">List of Submissions</h5>
                @if (Auth::check() && Auth::user()->roles->isNotEmpty())
                    @if (Auth::user()->roles->pluck('name')->contains('prepared'))
                        <div class="mb-3">
                            <a href="{{ route('submissions.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-square"></i> Add Submission
                            </a>
                        </div>
                    @endif
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-bordered datatable">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col" class="text-center">Bagian</th>
                                <th scope="col" class="text-center">No Transaksi</th>
                                <th scope="col" class="text-center">Title</th>
                                <th scope="col" class="text-center">Remark</th>
                                <th scope="col" class="text-center">Status</th>
                                <th scope="col" class="text-center">Attachment</th>
                                <th scope="col" class="text-center">Prepare</th>
                                <th scope="col" class="text-center">Date Submission</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($submissions as $submission)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $submission->departement->nama_departement ?? 'Unknown' }}</td>
                                    <td>{{ $submission->no_transaksi }}</td>
                                    <td>{{ $submission->title }}</td>
                                    <td>{{ $submission->remark }}</td>
                                    <td>
                                        @if (Auth::check() &&
                                                Auth::user()->roles->pluck('name')->intersect(['superadmin', 'prepared', 'Check1', 'Check2', 'approved'])->isNotEmpty())
                                            @if ($submission->approvals->last())
                                                @php
                                                    $lastApproval = $submission->approvals->last();
                                                    $status = $lastApproval->status ?? 'Pending';

                                                    // Daftar role dengan prioritas tertinggi ke terendah
                                                    $rolePriorities = ['approved', 'Check2', 'Check1', 'prepared'];

                                                    // Ambil role pengguna yang sesuai dengan prioritas tertinggi
                                                    $userRoles = $lastApproval->user->roles->pluck('name')->toArray();
                                                    $highestRole =
                                                        collect($rolePriorities)->first(function ($role) use (
                                                            $userRoles,
                                                        ) {
                                                            return in_array($role, $userRoles);
                                                        }) ?? 'Unknown';

                                                    $approvalDate = $lastApproval->approved_date
                                                        ? \Carbon\Carbon::parse($lastApproval->approved_date)->format(
                                                            'd M Y H:i:s',
                                                        )
                                                        : 'N/A';

                                                    // Tentukan teks status
                                                    $statusText = match (true) {
                                                        $status === 'approved' => 'Approved by ' .
                                                            ucfirst($highestRole),
                                                        $status === 'rejected' => 'Rejected',
                                                        default => 'Pending',
                                                    };

                                                    // Tentukan kelas badge berdasarkan status
                                                    $badgeClass = match (true) {
                                                        $status === 'approved' => 'bg-success',
                                                        $status === 'rejected' => 'bg-danger',
                                                        default => 'bg-secondary',
                                                    };
                                                @endphp

                                                <span class="badge {{ $badgeClass }}"
                                                    title="Last approval by {{ ucfirst($highestRole) }}">
                                                    {{ $statusText }}
                                                </span>
                                                <br>
                                                <small class="text-muted">Date: {{ $approvalDate }}</small>
                                            @else
                                                <span class="badge bg-secondary">Pending</span>
                                            @endif
                                        @endif
                                    </td>
                                    @if (Auth::check() && Auth::user()->roles->isNotEmpty())
                                        @if (Auth::user()->roles->pluck('name')->intersect(['superadmin', 'prepared', 'Check1', 'Check2', 'approved'])->isNotEmpty())
                                            <td>
                                                <button class="btn btn-info btn-sm"
                                                    onclick="openApprovalModal({{ $submission->id }}, '{{ asset($submission->lampiran_pdf) }}')">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            </td>
                                        @endif
                                        </td>
                                        <td>{{ $submission->user->name }}|{{ $submission->user->departement->nama_departement }}
                                        <td>{{ \Carbon\Carbon::parse($submission->created_at)->format('d M Y H:i:s') }}
                                        </td>
                                        @if ($roleNames->contains('prepared') && $submission->id_user === auth()->id())
                                            <td>
                                                <form action="{{ route('submissions.destroy', $submission->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus submission ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        @else
                                            <td> - </td>
                                        @endif
                                    @endif

                                </tr>
                            @empty
                        </tbody>

                        @endforelse
                    </table>
                </div>
                {{-- style --}}
                <style>
                    /* Atur margin dan padding di tabel */
                    #approvalTableBody tr {
                        margin: 0;
                        padding: 0;
                    }

                    #approvalTableBody td,
                    #approvalTableBody th {
                        padding: 5px 10px;
                        /* Kurangi padding untuk lebih rapat */
                        font-size: 14px;
                        /* Ukuran font lebih kecil untuk tabel */
                    }

                    /* Atur jarak antar baris */
                    #approvalTableBody tr {
                        line-height: 1.2;
                        /* Atur tinggi baris */
                    }

                    /* Kurangi jarak di antara header tabel dan isi */
                    .modal-body table {
                        margin-bottom: 10px;
                    }

                    /* Atur margin modal lebih rapat */
                    .modal-body {
                        padding-top: 10px;
                        padding-bottom: 10px;
                    }

                    /* Jarak antar elemen dalam modal */
                    .modal-body>* {
                        margin-bottom: 10px;
                    }

                    .pdf-container {
                        height: 80vh;
                        /* Sesuaikan tinggi ke viewport */
                        overflow-y: auto;
                        /* Tambahkan scroll jika kontennya panjang */
                        background-color: #f8f9fa;
                        border: 1px solid #ddd;
                        padding: 20px;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                    }

                    .pdf-container canvas {
                        margin-bottom: 20px;
                        /* Beri jarak antar halaman */
                        border: 1px solid #ccc;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    }


                    @media (max-width: 768px) {
                        .pdf-container {
                            height: 80vh;
                            /* Sesuaikan tinggi pada layar kecil */
                        }
                    }
                </style>


                {{-- modal view --}}
                <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="approvalModalLabel">Approval Submission</h5>
                                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered approval-table">
                                        <thead>
                                            <tr>
                                                <th>Approval Stage</th>
                                                <th>Status</th>
                                                <th>Approved By</th>
                                                <th>Approval Date</th>
                                                <th>Approval Remark</th>
                                            </tr>
                                        </thead>
                                        <tbody id="approvalTableBody">
                                            <!-- Data akan diisi oleh JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                                <!-- PDF Viewer -->
                                <div id="pdfViewerContainer" class="pdf-container rounded shadow-sm"></div>

                                <!-- Form Status -->
                                <div class="mt-3" id="statusFormContainer">
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

                                <!-- Remark Input -->
                                <div class="mt-3" id="remarkContainer">
                                    <label for="remark">Remark:<small class="text-danger"> Required if NO</small></label>
                                    <textarea id="remark" class="form-control" rows="3" placeholder="Enter your remark"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="submitButton"
                                    onclick="submitApproval()">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js"></script>

                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    // Fungsi untuk mengisi tabel persetujuan di modal
                    function populateApprovalTable(submissionId) {
                        const approvalTableBody = document.getElementById('approvalTableBody');
                        approvalTableBody.innerHTML = ''; // Kosongkan tabel sebelum diisi ulang

                        fetch(`/modal/${submissionId}/approval-table`) // Route untuk mengambil data persetujuan
                            .then((response) => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then((data) => {
                                if (!data.approvals || data.approvals.length === 0) {
                                    approvalTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">No data available</td>
                    </tr>
                `;
                                    return;
                                }

                                data.approvals.forEach((approval) => {
                                    const row = `
                    <tr>
                        <td>${approval.stage}</td>
                        <td>
                            <span class="badge ${
                                approval.status === "approved"
                                    ? "bg-success"
                                    : approval.status === "rejected"
                                    ? "bg-danger"
                                    : "bg-secondary"
                            }">
                                ${approval.status}
                            </span>
                        </td>
                        <td>${approval.approved_by}</td>
                        <td>${approval.date}</td>
                        <td>${approval.remark}</td>
                    </tr>
                `;
                                    approvalTableBody.insertAdjacentHTML("beforeend", row);
                                });
                            })
                            .catch((error) => {
                                console.error('Error fetching approval data:', error);
                            });
                    }

                    // Fungsi untuk membuka modal persetujuan
                    function openApprovalModal(submissionId, pdfPath) {
                        // Pastikan elemen modal tersedia sebelum manipulasi
                        const pdfViewerContainer = document.getElementById('pdfViewerContainer');
                        if (pdfViewerContainer) {
                            pdfViewerContainer.setAttribute('data-id', submissionId);
                        } else {
                            console.error('Elemen pdfViewerContainer tidak ditemukan');
                        }

                        // Fetch data tabel persetujuan
                        populateApprovalTable(submissionId);

                        // Fetch data untuk form validasi
                        fetch(`/get-approval-data/${submissionId}`)
                            .then((response) => response.json())
                            .then((data) => {
                                const {
                                    canApprove
                                } = data;

                                const statusFormContainer = document.getElementById('statusFormContainer');
                                const remarkContainer = document.getElementById('remarkContainer');
                                const submitButton = document.getElementById('submitButton');

                                // Reset form
                                document.getElementById('approveStatus').checked = false;
                                document.getElementById('rejectStatus').checked = false;
                                document.getElementById('remark').value = '';

                                // Menampilkan atau menyembunyikan form berdasarkan hak akses
                                if (!canApprove) {
                                    statusFormContainer.style.display = 'none';
                                    remarkContainer.style.display = 'none';
                                    submitButton.style.display = 'none';
                                } else {
                                    statusFormContainer.style.display = 'block';
                                    remarkContainer.style.display = 'block';
                                    submitButton.style.display = 'block';
                                }

                                // Tampilkan PDF menggunakan PDF.js
                                renderPDF(pdfPath);

                                // Tampilkan modal
                                const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
                                modal.show();
                            })
                            .catch((error) => {
                                console.error('Error fetching approval data:', error);
                            });
                    }

                    // Fungsi untuk merender PDF menggunakan PDF.js
                    // Fungsi untuk merender PDF menggunakan PDF.js
                    function renderPDF(pdfPath) {
                        const container = document.getElementById('pdfViewerContainer');
                        if (!container) {
                            console.error('Elemen pdfViewerContainer tidak ditemukan');
                            return;
                        }

                        container.innerHTML = ''; // Kosongkan sebelum render

                        pdfjsLib.getDocument(pdfPath).promise.then((pdf) => {
                            const totalPages = pdf.numPages;

                            for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                                pdf.getPage(pageNum).then((page) => {
                                    const viewport = page.getViewport({
                                        scale: 1.2
                                    });
                                    const canvas = document.createElement('canvas');
                                    const context = canvas.getContext('2d');

                                    canvas.height = viewport.height;
                                    canvas.width = viewport.width;

                                    container.appendChild(canvas); // Tambahkan canvas ke container

                                    const renderContext = {
                                        canvasContext: context,
                                        viewport: viewport,
                                    };

                                    page.render(renderContext).promise.then(() => {
                                        console.log(`Halaman ${pageNum} selesai dirender.`);
                                    });
                                });
                            }
                        }).catch((error) => {
                            console.error('Error loading PDF:', error);
                            container.innerHTML = '<p class="text-danger">Unable to load PDF.</p>';
                        });
                    }



                    // Fungsi untuk submit persetujuan
                    function submitApproval() {
                        const status = document.querySelector('input[name="approvalStatus"]:checked');
                        const remark = document.getElementById('remark').value;
                        const pdfViewerContainer = document.getElementById('pdfViewerContainer');

                        if (!pdfViewerContainer) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Data tidak ditemukan, coba lagi.',
                            });
                            return;
                        }

                        const submissionId = pdfViewerContainer.getAttribute('data-id');

                        if (!status) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Silakan pilih status (YES atau NO).',
                            });
                            return;
                        }

                        if (status.value === 'rejected' && !remark.trim()) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Kesalahan',
                                text: 'Alasan wajib diisi jika memilih NO.',
                            });
                            return;
                        }

                        const submissionData = {
                            id_submission: submissionId,
                            status: status.value,
                            remark: remark,
                            _token: '{{ csrf_token() }}',
                        };

                        Swal.fire({
                            title: 'Konfirmasi',
                            text: `Apakah Anda yakin ingin ${status.value === 'approved' ? 'MENYETUJUI' : 'MENOLAK'} dokumen ini?`,
                            icon: status.value === 'approved' ? 'success' : 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal',
                        }).then((result) => {
                            if (!result.isConfirmed) return;

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
                                    }).then(() => {
                                        location.reload();
                                    });
                                })
                                .catch((error) => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Kesalahan',
                                        text: error.message,
                                    });
                                });
                        });
                    }
                </script>


    </section>
@endsection
