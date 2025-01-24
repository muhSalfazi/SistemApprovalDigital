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

            <form id="submissionForm" action="{{ route('submissions.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_user" value="{{ auth()->user()->id }}">

                {{-- Department --}}
                <div class="mb-3">
                    <label for="departement_name" class="form-label">Department</label>
                    <input type="text" class="form-control" value="{{ $departements->first()->nama_departement ?? '' }}"
                        readonly>
                    <input type="hidden" name="id_departement" value="{{ encrypt($departements->first()->id ?? '') }}">
                </div>

                {{-- Category --}}
                <div class="mb-3">
                    <label for="kategori_name" class="form-label">Category</label>
                    <input type="text" class="form-control" value="{{ $categories->first()->nama_kategori ?? '' }}"
                        readonly>
                    <input type="hidden" name="id_kategori" value="{{ encrypt($categories->first()->id ?? '') }}">
                </div>

                {{-- Title --}}
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                        value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- No Transaksi --}}
                <div class="mb-3">
                    <label for="no_transaksi" class="form-label">No Transaksi</label>
                    <input type="text" id="no_transaksi" name="no_transaksi" class="form-control" readonly>
                    @error('no_transaksi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remark --}}
                <div class="mb-3">
                    <label for="remark" class="form-label">Remark</label>
                    <textarea name="remark" id="remark" class="form-control @error('remark') is-invalid @enderror"
                        rows="3">{{ old('remark') }}</textarea>
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

{{-- JS No Transaksi Generate --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const departement = document.querySelector('input[name="id_departement"]').value;
        const kategori = document.querySelector('input[name="id_kategori"]').value;

        if (departement && kategori) {
            fetch('{{ route('generateTransactionNumber') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id_departement: departement,
                    id_kategori: kategori
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.no_transaksi) {
                        document.getElementById('no_transaksi').value = data.no_transaksi;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menghasilkan nomor transaksi.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memproses permintaan.'
                    });
                });
        }
    });
</script>

{{-- end js --}}

{{-- sweetalert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('submitButton').addEventListener('click', function (event) {
        const form = document.getElementById('submissionForm');
        const title = document.getElementById('title').value;
        const remark = document.getElementById('remark').value;
        const lampiran = document.getElementById('lampiran_pdf').value;

        if (!title || !remark || !lampiran) {
            Swal.fire({
                icon: 'error',
                title: 'Form Belum Lengkap!',
                text: 'Pastikan semua data telah diisi dengan benar sebelum melanjutkan.',
            });
            return;
        }

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
                form.submit();
            }
        });
    });

</script>

@endsection
