@extends('layouts.app')

@section('title', 'Data Kategori')

@section('content')
    <div class="pagetitle">
        <h1>Data Kategori</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Data Kategori</li>
            </ol>
        </nav>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            {{ session('error') }}
        </div>
    @endif

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Data Kategori</h5>
                        <div class="mb-3">
                            <button class="btn btn-primary" onclick="createKategori()">
                                <i class="bi bi-plus-square"></i> Create New Kategori
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">NO</th>
                                        <th class="text-center">Nama Kategori</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kategoris as $kategori)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $kategori->nama_kategori }}</td>
                                            <td class="text-center">
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-primary btn-sm mt-2"
                                                    onclick="editKategori({{ $kategori->id }})">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>

                                                <!-- Tombol Delete -->
                                                <form action="{{ route('kategori.destroy', $kategori) }}" method="POST"
                                                    id="delete-form-{{ $kategori->id }}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete({{ $kategori->id }})"
                                                        class="btn btn-danger btn-sm mt-2">
                                                        <i class="bi bi-trash3"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="kategoriModal" tabindex="-1" aria-labelledby="kategoriModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="kategoriForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" id="_method" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="kategoriModalLabel">Tambah/Edit Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12 mb-3">
                            <label for="kategoriName" class="form-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="kategoriName" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Kategori</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function createKategori() {
            // Reset form
            document.getElementById('kategoriForm').reset();
            document.getElementById('kategoriForm').action = `{{ route('kategori.store') }}`;
            document.getElementById('kategoriModalLabel').innerText = "Create Kategori";

            // Kosongkan nilai _method
            document.getElementById('_method').value = ""; // Metode default adalah POST

            const modal = new bootstrap.Modal(document.getElementById('kategoriModal'));
            modal.show();
        }

        function editKategori(id) {
            const url = `{{ url('kategori') }}/${id}/edit`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Isi form dengan data kategori
                    document.getElementById('kategoriForm').action = `{{ url('kategori') }}/${id}`;
                    document.getElementById('kategoriModalLabel').innerText = "Edit Kategori";

                    // Setel metode ke PUT
                    document.getElementById('_method').value = "PUT";
                    document.getElementById('kategoriName').value = data.nama_kategori;

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('kategoriModal'));
                    modal.show();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
@endsection
