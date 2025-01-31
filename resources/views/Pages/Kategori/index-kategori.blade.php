@extends('layouts.app')

@section('title', 'Data Kategori')

@section('content')
    <div class="pagetitle animate__animated animate__fadeInLeft">
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
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal!',
                html: `
                <ul style="text-align: left;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            `,
            });
        </script>
    @endif

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title animate__animated animate__fadeInLeft">Data Kategori</h5>
                        <div class="mb-3">
                            <button class="btn btn-primary btn-sm" onclick="createKategori()">
                                <i class="bi bi-plus-square"></i> Create New Kategori
                            </button>
                        </div>

                        <div class="table-responsive animate__animated animate__fadeInUp">
                            <table class="table table-striped table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">NO</th>
                                        <th class="text-center">Nama Kategori</th>
                                        <th class="text-center">Alias</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Waktu Nonaktif</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kategoris as $kategori)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $kategori->nama_kategori }}</td>
                                            <td class="text-center">{{ $kategori->alias_name }}</td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex align-items-center">
                                                    <input class="form-check-input toggle-status text-center"
                                                        type="checkbox" data-id="{{ $kategori->id }}"
                                                        {{ is_null($kategori->deleted_at) ? 'checked' : '' }}>
                                                    <span class="ms-2 status-text text-center">
                                                        {{ is_null($kategori->deleted_at) ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                {{ $kategori->deleted_at ? \Carbon\Carbon::parse($kategori->deleted_at)->format('d M Y H:i:s') : '-' }}
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm mb-1"
                                                    style="font-size: 0.775rem; padding: 3px 8px;"
                                                    onclick="editKategori({{ $kategori->id }})"
                                                    {{ $kategori->deleted_at ? 'disabled' : '' }}>
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>

                                                <form action="{{ route('kategori.destroy', $kategori) }}" method="POST"
                                                    id="delete-form-{{ $kategori->id }}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete({{ $kategori->id }})"
                                                        class="btn btn-danger btn-sm mb-1"
                                                        style="font-size: 0.775rem; padding: 3px 8px;"
                                                        {{ $kategori->deleted_at ? 'disabled' : '' }}>
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
    <div class="modal fade animate__animated animate__fadeInDown" id="kategoriModal" tabindex="-1"
        aria-labelledby="kategoriModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="kategoriForm" method="POST" action="{{ route('kategori.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="_method" value="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="kategoriModalLabel">Tambah/Edit Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12 mb-3">
                            <label for="kategoriName" class="form-label">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="kategoriName" class="form-control"
                                value="{{ old('nama_kategori') }}" required>
                            @error('nama_kategori')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="alias_name" class="form-label">Alias Kategori</label>
                            <input type="text" name="alias_name" id="alias_name" class="form-control"
                                value="{{ old('alias_name') }}" required maxlength="4">
                            @error('alias_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
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
                    document.getElementById('alias_name').value = data.alias_name;

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('kategoriModal'));
                    modal.show();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>

    {{-- js status kategori --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.toggle-status').forEach(function(toggle) {
                toggle.addEventListener('change', function() {
                    let kategoriId = this.getAttribute('data-id');
                    let isChecked = this.checked;
                    let actionText = isChecked ? 'mengaktifkan' : 'menonaktifkan';
                    let statusText = isChecked ? 'Aktif' : 'Nonaktif';

                    // Ambil elemen teks status
                    let statusLabel = this.closest('td').querySelector('.status-text');
                    let deleteTimeCell = this.closest('tr').querySelector(
                        'td:nth-child(5)'); // Kolom waktu nonaktif

                    // Konfirmasi SweetAlert
                    Swal.fire({
                        title: 'Konfirmasi Perubahan Status',
                        text: `Apakah Anda yakin ingin ${actionText} kategori ini?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`{{ url('/kategori/toggle') }}/${kategoriId}`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: data.message
                                        });

                                        // Perbarui teks status
                                        statusLabel.textContent = statusText;

                                        // Perbarui waktu nonaktif jika dinonaktifkan
                                        if (!isChecked) {
                                            let now = new Date();
                                            let formattedTime = now.toLocaleDateString(
                                                    'id-ID') + ' ' + now
                                                .toLocaleTimeString('id-ID');
                                            deleteTimeCell.textContent = formattedTime;
                                        } else {
                                            deleteTimeCell.textContent = '-';
                                        }
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal',
                                            text: 'Terjadi kesalahan!'
                                        });
                                        toggle.checked = !
                                            isChecked; // Kembalikan toggle jika gagal
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Terjadi kesalahan pada server!'
                                    });
                                    toggle.checked = !
                                        isChecked; // Kembalikan toggle jika gagal
                                });
                        } else {
                            toggle.checked = !isChecked; // Batalkan perubahan
                        }
                    });
                });
            });
        });
    </script>

    {{-- softdelete --}}
    {{-- alert softdelete --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Tangani tombol Edit
            document.querySelectorAll('.edit-btn').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    if (this.hasAttribute('disabled')) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Kategori Nonaktif',
                            text: 'Aktifkan terlebih dahulu sebelum mengedit kategori ini!',
                        });
                    }
                });
            });

            // Tangani tombol Delete
            document.querySelectorAll('.delete-btn').forEach(function (button) {
                button.addEventListener('click', function (event) {
                    if (this.hasAttribute('disabled')) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Kategori Nonaktif',
                            text: 'Aktifkan terlebih dahulu sebelum menghapus Kategori ini!',
                        });
                    }
                });
            });
        });
    </script>
@endsection
