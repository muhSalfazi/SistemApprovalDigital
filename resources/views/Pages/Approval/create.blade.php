@extends('layouts.app')

@section('title', 'Create Submission')

@section('content')
    <div class="pagetitle">
        <h1>Create Submission</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('submissions.index') }}">Submissions</a></li>
                <li class="breadcrumb-item active">Create Submission</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Submission Form</h5>

                <form id="submissionForm" action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_user" value="{{ auth()->user()->id }}">

                    {{-- Department --}}
                    <div class="mb-3">
                        <label for="id_departement" class="form-label">Department</label>
                        <select name="id_departement" id="id_departement"
                            class="form-select @error('id_departement') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih Department</option>
                            @foreach ($departements as $departement)
                                <option value="{{ $departement->id }}"
                                    {{ old('id_departement') == $departement->id ? 'selected' : '' }}>
                                    {{ $departement->nama_departement }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_departement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="mb-3">
                        <label for="id_kategori" class="form-label">Category</label>
                        <select name="id_kategori" id="id_kategori"
                            class="form-select @error('id_kategori') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('id_kategori') == $category->id ? 'selected' : '' }}>
                                    {{ $category->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Title --}}
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- No Transaksi --}}
                    <div class="mb-3">
                        <label for="no_transaksi" class="form-label">No Transaksi</label>
                        <input type="text" class="form-control @error('no_transaksi') is-invalid @enderror"
                            id="no_transaksi" name="no_transaksi" value="{{ old('no_transaksi') }}" readonly>
                        @error('no_transaksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remark --}}
                    <div class="mb-3">
                        <label for="remark" class="form-label">Remark</label>
                        <textarea name="remark" id="remark" class="form-control @error('remark') is-invalid @enderror" rows="3">{{ old('remark') }}</textarea>
                        @error('remark')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Lampiran PDF --}}
                    <div class="mb-3">
                        <label for="lampiran_pdf" class="form-label">Lampiran (PDF)</label>
                        <input type="file" class="form-control @error('lampiran_pdf') is-invalid @enderror"
                            id="lampiran_pdf" name="lampiran_pdf" accept="application/pdf">
                        @error('lampiran_pdf')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="button" class="btn btn-primary" id="submitButton">Submit</button>
                </form>

            </div>
        </div>
    </section>

    {{-- js no transaksi generate --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departementSelect = document.getElementById('id_departement');
            const noTransaksiInput = document.getElementById('no_transaksi');

            function updateTransactionNumber() {
                const departementId = departementSelect.value;

                if (departementId) {
                    const url =
                        `{{ route('submissions.generateTransactionNumber') }}?id_departement=${departementId}`;

                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            noTransaksiInput.value = data.no_transaksi;
                        })
                        .catch(error => {
                            console.error('Error fetching transaction number:', error);
                        });
                }
            }

            departementSelect.addEventListener('change', updateTransactionNumber);
            kategoriSelect.addEventListener('change', updateTransactionNumber);
        });
    </script>
    {{-- end js --}}

    {{-- sweetalert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('submitButton').addEventListener('click', function(event) {
            const form = document.getElementById('submissionForm');
            const departement = document.getElementById('id_departement').value;
            const kategori = document.getElementById('id_kategori').value;
            const title = document.getElementById('title').value;
            const remark = document.getElementById('remark').value;
            const lampiran = document.getElementById('lampiran_pdf').value;

            // Validasi input sebelum menampilkan konfirmasi
            if (!departement || !kategori || !title || !remark || !lampiran) {
                Swal.fire({
                    icon: 'error',
                    title: 'Form Belum Lengkap!',
                    text: 'Pastikan semua data telah diisi dengan benar sebelum melanjutkan.',
                });
                return;
            }

            // Tampilkan konfirmasi sebelum submit
            Swal.fire({
                title: 'Konfirmasi Pengisian',
                text: "Apakah Anda yakin semua data sudah diisi dengan benar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form secara manual
                    form.submit();
                }
            });
        });
    </script>

@endsection
