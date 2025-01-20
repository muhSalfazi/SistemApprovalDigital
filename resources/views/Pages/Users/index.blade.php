@extends('layouts.app')

@section('title', 'Data Users')

@section('content')
    <div class="pagetitle">
        <h1>Data Users</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Data Users</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
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
                        <h5 class="card-title">Data Users</h5>
                        <div class="mb-3">
                            <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="bi bi-plus-square">
                                    Create New User</i></a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" class="text-center">NO</th>
                                        <th scope="col" class="text-center">Bagian</th>
                                        <th scope="col" class="text-center">Id-Card</th>
                                        <th scope="col" class="text-center">Nama</th>
                                        <th scope="col" class="text-center">Email</th>
                                        <th scope="col" class="text-center">Role</th>
                                        <th scope="col" class="text-center">Last Login</th>
                                        <th scope="col" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $user->departement->nama_departement ?? '-' }}</td>
                                            <td class="text-center">{{ $user->IDcard }}</td>
                                            <td class="text-center">{{ $user->name }}</td>
                                            <td class="text-center">{{ $user->email }}</td>
                                            <td class="text-center">{{ $user->roles->pluck('name')->implode(', ') }}</td>

                                            <td class="text-center">
                                                @if ($user->last_login && \Carbon\Carbon::parse($user->last_login)->isValid())
                                                    {{ \Carbon\Carbon::parse($user->last_login)->diffForHumans() }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-success  btn-sm mt-1"
                                                    onclick="editUser({{ $user->id }})">
                                                    <i class="bi bi-pencil-square"></i> Edit User
                                                </button>
                                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                    id="delete-form-{{ $user->id }}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete({{ $user->id }})"
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
        <!-- Modal -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editUserForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editName" class="form-label">Name</label>
                                <input type="text" name="name" id="editName" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" name="email" id="editEmail" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="ID-card" class="form-label">ID-Card</label>
                                <input type="text" name="ID-card" id="ID-card" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="editRole" class="form-label">Role</label>
                                <select name="role" id="editRole" class="form-select">
                                    <!-- Options akan diisi melalui JavaScript -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="editPassword" class="form-label">Password</label>
                                <input type="text" name="password" id="editPassword" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    {{-- js --}}
    <script>
        function editUser(userId) {
            const url = `/users/${userId}/edit`; // Endpoint untuk fetch data user

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Perbarui form action untuk update
                    document.getElementById('editUserForm').action = `/users/${userId}`;

                    // Isi data ke input form
                    document.getElementById('editName').value = data.user.name;
                    document.getElementById('editEmail').value = data.user.email;
                    document.getElementById('ID-card').value = data.user.IDcard;

                    // Kosongkan dropdown role sebelum diisi ulang
                    const roleSelect = document.getElementById('editRole');
                    roleSelect.innerHTML = `<option value="" disabled>Pilih Role</option>`;

                    // Tambahkan opsi role ke dropdown
                    data.roles.forEach(role => {
                        const option = document.createElement('option');
                        option.value = role.name;
                        option.textContent = role.name.charAt(0).toUpperCase() + role.name.slice(1);

                        // Disable role yang sudah dimiliki
                        if (data.userRoles.includes(role.name)) {
                            option.disabled = true;
                            option.selected = true; // Pilih role yang sudah dimiliki
                        }

                        roleSelect.appendChild(option);
                    });

                    // Simpan role saat ini untuk perbandingan nanti
                    roleSelect.dataset.originalRoles = JSON.stringify(data.userRoles);

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    modal.show();
                })
                .catch(error => console.error('Error fetching user data:', error));
        }

        // Tambahkan event listener pada form submit untuk konfirmasi
        document.getElementById('editUserForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const roleSelect = document.getElementById('editRole');
            const selectedRoles = Array.from(roleSelect.selectedOptions).map(option => option.value);
            const originalRoles = JSON.parse(roleSelect.dataset.originalRoles || '[]');

            const newRoles = selectedRoles.filter(role => !originalRoles.includes(role));

            if (newRoles.length > 0) {
                Swal.fire({
                    title: 'Konfirmasi Perubahan Role',
                    text: "Role yang sudah dipilih tidak dapat dihapus setelah disimpan. Apakah Anda yakin?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('editUserForm').submit();
                    }
                });
            } else {
                document.getElementById('editUserForm').submit();
            }
        });
    </script>


@endsection
