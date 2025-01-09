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
                <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id_user" value="{{ auth()->user()->id }}">

                    <div class="mb-3">
                        <label for="id_departement" class="form-label">Department</label>
                        <select name="id_departement" id="id_departement" class="form-select" required>
                            <option value="" disabled selected>Pilih Department</option>
                            @foreach ($departements as $departement)
                                <option value="{{ $departement->id }}">{{ $departement->nama_departement }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_kategori" class="form-label">Category</label>
                        <select name="id_kategori" id="id_kategori" class="form-select" required>
                            <option value="" disabled selected>Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="no_transaksi" class="form-label">No Transaksi</label>
                        <input type="text" class="form-control" id="no_transaksi" name="no_transaksi"
                            value="{{ old('no_transaksi') }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="remark" class="form-label">Remark</label>
                        <textarea name="remark" id="remark" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="lampiran_pdf" class="form-label">Lampiran (PDF)</label>
                        <input type="file" class="form-control" id="lampiran_pdf" name="lampiran_pdf"
                            accept="application/pdf">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departementSelect = document.getElementById('id_departement');
            const kategoriSelect = document.getElementById('id_kategori');
            const noTransaksiInput = document.getElementById('no_transaksi');

            function updateTransactionNumber() {
                const departementId = departementSelect.value;
                const kategoriId = kategoriSelect.value;

                if (departementId && kategoriId) {
                    const url =
                        `{{ route('submissions.generateTransactionNumber') }}?id_departement=${departementId}&id_kategori=${kategoriId}`;

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
@endsection
