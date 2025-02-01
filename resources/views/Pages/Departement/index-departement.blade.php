@extends('layouts.app')

@section('title', 'Data Departemen')

@section('content')
    <div class="pagetitle animate__animated animate__fadeInLeft">
        <h1>Data Departemen</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Data Departemen</li>
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
                        <h5 class="card-title animate__animated animate__fadeInLeft">Data Departemen</h5>
                        <div class="mb-3">
                            <button class="btn btn-primary btn-sm" onclick="createDepartemen()">
                                <i class="bi bi-plus-square"></i> Create New Departemen
                            </button>
                        </div>

                        <div class="table-responsive animate__animated animate__fadeInUp">
                            <table class="table table-striped table-bordered datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">NO</th>
                                        <th class="text-center">Nama Departement</th>
                                        <th class="text-center">Deksripsi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Waktu Nonaktif</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($Departments as $departement)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $departement->nama_departement }}</td>
                                            <td class="text-center">{{ $departement->deksripsi ?? '-'}}</td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input toggle-status"
                                                    data-id="{{ $departement->id }}"
                                                    {{ is_null($departement->deleted_at) ? 'checked' : '' }}>

                                                    <span class="ms-2 status-text text-center">
                                                        {{ is_null($departement->deleted_at) ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                {{ $departement->deleted_at ? \Carbon\Carbon::parse($departement->deleted_at)->format('d M Y H:i:s') : '-' }}
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm edit-btn mb-1"
                                                    style="font-size: 0.775rem; padding: 3px 8px;"
                                                    onclick="editdepartement({{ $departement->id }})"
                                                    {{ $departement->deleted_at ? 'disabled' : '' }}>
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </button>

                                                <form action="{{ route('departement.destroy', $departement) }}" method="POST"
                                                    id="delete-form-{{ $departement->id }}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-sm delete-btn mb-1"
                                                        onclick="confirmDelete({{ $departement->id }})"
                                                        style="font-size: 0.775rem; padding: 3px 8px;"
                                                        {{ $departement->deleted_at ? 'disabled' : '' }}>
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
    <div class="modal fade animate__animated animate__fadeInDown" id="departementModal" tabindex="-1"
        aria-labelledby="departementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="departementform" method="POST" action="{{ route('departement.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="_method" value="">

                    <div class="modal-header">
                        <h5 class="modal-title" id="departementModalLabel">Tambah/Edit Departement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12 mb-3">
                            <label for="departementName" class="form-label">Nama Departemen</label>
                            <input type="text" name="nama_departement" id="departementName" class="form-control"
                                value="{{ old('nama_departement') }}" required maxlength="4" placeholder="max:4">
                            @error('nama_departement')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="departementDeksripsi" class="form-label">Deksripsi</label>
                            <input type="text" name="deksripsi" id="departementDeksripsi" class="form-control"
                                value="{{ old('deksripsi') }}" required  placeholder="-">
                            @error('deksripsi')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Departement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        function createDepartemen() {
            // Reset form
            document.getElementById('departementform').reset();
            document.getElementById('departementform').action = `{{ route('departement.store') }}`;
            document.getElementById('departementModalLabel').innerText = "Create Departement";

            // Kosongkan nilai _method
            document.getElementById('_method').value = ""; // Metode default adalah POST

            const modal = new bootstrap.Modal(document.getElementById('departementModal'));
            modal.show();
        }

        function editdepartement(id) {
            const url = `{{ url('departement') }}/${id}/edit`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Isi form dengan data departement
                    document.getElementById('departementform').action = `{{ url('departement') }}/${id}`;
                    document.getElementById('departementModalLabel').innerText = "Edit Departement";

                    // Setel metode ke PUT
                    document.getElementById('_method').value = "PUT";
                    document.getElementById('departementName').value = data.nama_departement;
                    document.getElementById('departementDeksripsi').value = data.deksripsi;

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('departementModal'));
                    modal.show();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>


    {{-- js status departement --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.toggle-status').forEach(function (toggle) {
                toggle.addEventListener('change', function () {
                    let departementID = this.getAttribute('data-id');
                    let isChecked = this.checked;
                    let actionText = isChecked ? 'mengaktifkan' : 'menonaktifkan';
                    let statusText = isChecked ? 'Active' : 'Inactive';

                    // Ambil elemen teks status
                    let statusLabel = this.closest('td').querySelector('.status-text');
                    let deleteTimeCell = this.closest('tr').querySelector('td:nth-child(4)'); // Kolom waktu nonaktif

                    // Debugging: Cek apakah elemen ditemukan
                    console.log("Toggle diklik, ID Departemen:", departementID);

                    // Konfirmasi SweetAlert
                    Swal.fire({
                        title: 'Konfirmasi Perubahan Status',
                        text: `Apakah Anda yakin ingin ${actionText} departemen ini?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            console.log("Mengirim AJAX request...");
                            fetch(`{{ url('/departement/toggle') }}/${departementID}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log("Response dari server:", data);
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
                                        let formattedTime = now.toLocaleDateString('id-ID') + ' ' + now.toLocaleTimeString('id-ID');
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
                                    toggle.checked = !isChecked; // Kembalikan toggle jika gagal
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan pada server!'
                                });
                                toggle.checked = !isChecked; // Kembalikan toggle jika gagal
                            });
                        } else {
                            toggle.checked = !isChecked; // Batalkan perubahan
                        }
                    });
                });
            });
        });
    </script>
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
                            title: 'Departemen Nonaktif',
                            text: 'Aktifkan terlebih dahulu sebelum mengedit departemen ini!',
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
                            title: 'Departemen Nonaktif',
                            text: 'Aktifkan terlebih dahulu sebelum menghapus departemen ini!',
                        });
                    }
                });
            });
        });
    </script>


@endsection
