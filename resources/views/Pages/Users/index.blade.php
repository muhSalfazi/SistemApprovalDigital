@extends('layouts.app')

@section('title', 'Data Users')

@section('content')
    <div class="pagetitle animate__animated animate__fadeInLeft">
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
    {{-- @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session('error') }}
    </div>
@endif --}}

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="section">

        <div class="row">

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title animate__animated animate__fadeInLeft">Data Users</h5>
                        <div class="mb-3">
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm"><i
                                    class="bi bi-plus-square">
                                    Create New User</i></a>
                        </div>

                        <div class="table-responsive animate__animated animate__fadeInUp">
                            <table class="table table-striped table-bordered datatable">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col" class="text-center">NO</th>
                                        <th scope="col" class="text-center">Bagian</th>
                                        <th scope="col" class="text-center">Kategori Akses</th>
                                        <th scope="col" class="text-center">Id-Card</th>
                                        <th scope="col" class="text-center">RFID</th>
                                        <th scope="col" class="text-center">Nama</th>
                                        <th scope="col" class="text-center">Email</th>
                                        <th scope="col" class="text-center">Role</th>
                                        <th scope="col" class="text-center">Last Login</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">{{ $user->departement->nama_departement ?? '-' }}</td>
                                            <td class="text-center">
                                                {{ $user->kategoris->pluck('alias_name')->implode(', ') ?: '-' }}
                                            </td>


                                            <td class="text-center">{{ $user->IDcard }}</td>
                                            <td class="text-center">{{ $user->RFID }}</td>
                                            <td class="text-center">{{ $user->name }}</td>
                                            <td class="text-center">{{ $user->email }}</td>
                                            <td class="text-center">{{ $user->roles->pluck('name')->implode(', ') ?: '-'}} </td>

                                            <td class="text-center">
                                                @if ($user->last_login && \Carbon\Carbon::parse($user->last_login)->isValid())
                                                    {{ \Carbon\Carbon::parse($user->last_login)->diffForHumans() }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (auth()->user()->roles->contains('name', 'superadmin'))
                                                    <form action="{{ route('users.toggleStatus', $user->id) }}"
                                                        method="POST" id="status-form-{{ $user->id }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="form-check form-switch d-flex justify-content-center">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="statusToggle{{ $user->id }}"
                                                                onchange="confirmStatusChange({{ $user->id }}, this)"
                                                                {{ $user->status ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="statusToggle{{ $user->id }}">
                                                                {{ $user->status ? 'Active' : 'Inactive' }}
                                                            </label>
                                                        </div>
                                                    </form>
                                                @else
                                                    <span class="badge {{ $user->status ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $user->status ? 'Active' : 'Inactive' }}
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="text-center">
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-success btn-sm mb-1"
                                                    style="font-size: 0.775rem; padding: 3px 8px;"
                                                    onclick="editUser({{ $user->id }})">
                                                    <i class="bi bi-pencil-square"></i> Edit User
                                                </button>

                                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                    id="delete-form-{{ $user->id }}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmDelete({{ $user->id }})"
                                                        class="btn btn-danger btn-sm "
                                                        style="font-size: 0.775rem; padding: 3px 8px;">
                                                        <i class="bi bi-trash3"></i> Delete
                                                    </button>
                                                </form>
                                                <button class="btn btn-warning btn-sm mt-1"
                                                style="font-size: 0.8rem; padding: 4px 10px;"
                                                onclick="openDeleteRoleKategoriModal({{ $user->id }})">
                                                <i class="bi bi-shield-x"></i> Kelola Akses
                                            </button>

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
        <div class="modal fade animate__animated animate__fadeInDown" id="editUserModal" tabindex="-1"
            aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="editUserForm" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editName" class="form-label">Name</label>
                                <input type="text" name="name" id="editName" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="editEmail" class="form-label">Email</label>
                                <input type="email" name="email" id="editEmail" class="form-control" readonly>
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
                                <label for="editKategori" class="form-label">Kategori</label>
                                <select name="kategori_id" id="editKategori" class="form-select">
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

        <!-- Modal Konfirmasi Hapus Role/Kategori -->
        <div class="modal fade" id="deleteRoleKategoriModal" tabindex="-1"
            aria-labelledby="deleteRoleKategoriModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteRoleKategoriModalLabel">Hapus Role/Kategori</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Pilih Role atau Kategori yang ingin dihapus:</p>
                        <input type="hidden" id="deleteUserId">
                        <div class="mb-3">
                            <label for="deleteRole" class="form-label">Role</label>
                            <select id="deleteRole" class="form-select">
                                <option value="" selected>- Pilih Role -</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="deleteKategori" class="form-label">Kategori</label>
                            <select id="deleteKategori" class="form-select">
                                <option value="" selected>- Pilih Kategori -</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteRoleKategori">Hapus</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- js delete role/kategori --}}
        <script>
            function openDeleteRoleKategoriModal(userId) {
                document.getElementById('deleteUserId').value = userId;

                fetch(`/users/${userId}/roles-kategories`)
                    .then(response => response.json())
                    .then(data => {
                        // Kosongkan dropdown sebelum diisi ulang
                        let roleSelect = document.getElementById('deleteRole');
                        roleSelect.innerHTML = `<option value="" selected>- Pilih Role -</option>`;
                        data.roles.forEach(role => {
                            roleSelect.innerHTML += `<option value="${role.id}">${role.name}</option>`;
                        });

                        let kategoriSelect = document.getElementById('deleteKategori');
                        kategoriSelect.innerHTML = `<option value="" selected>- Pilih Kategori -</option>`;
                        data.kategories.forEach(kategori => {
                            kategoriSelect.innerHTML +=
                                `<option value="${kategori.id}">${kategori.nama_kategori}</option>`;
                        });

                        var deleteModal = new bootstrap.Modal(document.getElementById('deleteRoleKategoriModal'));
                        deleteModal.show();
                    })
                    .catch(error => console.error('Error fetching user roles & categories:', error));
            }

            document.getElementById('confirmDeleteRoleKategori').addEventListener('click', function() {
                let userId = document.getElementById('deleteUserId').value;
                let selectedRole = document.getElementById('deleteRole').value;
                let selectedKategori = document.getElementById('deleteKategori').value;

                if (!selectedRole && !selectedKategori) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih salah satu!',
                        text: 'Silakan pilih role atau kategori yang ingin dihapus!',
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: "Apakah Anda yakin ingin menghapus data ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        let requestData = {
                            user_id: userId,
                            role_id: selectedRole ? selectedRole : null,
                            kategori_id: selectedKategori ? selectedKategori : null,
                        };

                        fetch(`/users/${userId}/delete-role-kategori`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(requestData)
                            })
                            .then(response => {
                                if (response.ok) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Data berhasil dihapus.',
                                        timer: 6000,
                                        showConfirmButton: true,
                                        timerProgressBar: true,
                                        showClass: {
                                            popup: 'animate__animated animate__fadeInDown'
                                        },
                                        hideClass: {
                                            popup: 'animate__animated animate__fadeOutUp'
                                        },
                                        customClass: {
                                            popup: 'small-swal-popup'
                                        }
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: 'Terjadi kesalahan saat menghapus data.',
                                        showConfirmButton: true
                                    });
                                }
                            })
                            .catch(error => console.error('Error:', error));
                    }
                });
            });
        </script>

        {{-- js status --}}
        <script>
            function confirmStatusChange(userId, checkbox) {
                Swal.fire({
                    title: 'Konfirmasi Perubahan Status',
                    text: 'Apakah Anda yakin ingin mengubah status pengguna ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Ubah',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('status-form-' + userId).submit();
                    } else {
                        checkbox.checked = !checkbox.checked;
                    }
                });
            }
        </script>
        {{-- js --}}
        <script>
            function editUser(userId) {
                const url = `/users/${userId}/edit`; // Endpoint untuk fetch data user

                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('editUserForm').action = `/users/${userId}`;
                        document.getElementById('editName').value = data.user.name;
                        document.getElementById('editEmail').value = data.user.email;
                        document.getElementById('ID-card').value = data.user.IDcard;

                        // Kosongkan dropdown role sebelum diisi ulang
                        const roleSelect = document.getElementById('editRole');
                        roleSelect.innerHTML = `<option value="" disabled selected>Pilih Role</option>`;

                        data.roles.forEach(role => {
                            const option = document.createElement('option');
                            option.value = role.name;
                            option.textContent = role.name.charAt(0).toUpperCase() + role.name.slice(1);

                            // Jika role sudah dimiliki, disable dan pilih opsi tersebut
                            if (data.userRoles.includes(role.name)) {
                                option.disabled = true;
                                option.selected = true;
                            }

                            roleSelect.appendChild(option);
                        });

                        // Kosongkan dropdown kategori sebelum diisi ulang
                        const kategoriSelect = document.getElementById('editKategori');
                        kategoriSelect.innerHTML = `<option value="" disabled selected>Pilih Kategori</option>`;

                        data.categories.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.nama_kategori;

                            // Tandai kategori yang sudah dipilih oleh user dan disable
                            if (data.userCategories.includes(category.id)) {
                                option.disabled = true; // Disable kategori yang sudah dipilih
                                option.selected = true; // Pilih kategori yang sudah dimiliki
                            }

                            kategoriSelect.appendChild(option);
                        });

                        // Tampilkan modal
                        const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                        modal.show();
                    })
                    .catch(error => console.error('Error fetching user data:', error));
            }

            document.getElementById('editUserForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const roleSelect = document.getElementById('editRole');
                const selectedRoles = Array.from(roleSelect.selectedOptions).map(option => option.value);
                const originalRoles = JSON.parse(roleSelect.dataset.originalRoles || '[]');

                const kategoriSelect = document.getElementById('editKategori');
                const selectedKategori = kategoriSelect.value; // Mengambil nilai kategori yang dipilih

                const newRoles = selectedRoles.filter(role => !originalRoles.includes(role));

                // Periksa apakah ada role baru atau kategori baru yang ingin ditambahkan
                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: "Apakah Anda yakin semua data sudah diisi dengan benar?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('editUserForm').submit();
                    }
                });
            });
        </script>


    @endsection
