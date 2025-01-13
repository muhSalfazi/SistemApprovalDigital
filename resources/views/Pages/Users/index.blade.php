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
                                            <td class="text-center">{{ $user->name }}</td>
                                            <td class="text-center">{{ $user->email }}</td>
                                            <td class="text-center">{{ $user->role->name }}</td>
                                            <td class="text-center">
                                                @if ($user->last_login && \Carbon\Carbon::parse($user->last_login)->isValid())
                                                    {{ \Carbon\Carbon::parse($user->last_login)->diffForHumans() }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <!-- Tombol Edit -->
                                                <button class="btn btn-primary btn-sm mt-2"
                                                    onclick="editUser({{ $user->id }})">
                                                    <i class="bi bi-pencil-square"></i> Edit
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

                                        <!-- Modal -->
                                        <div class="modal fade" id="editUserModal" tabindex="-1"
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
                                                            <div class="col-md-12 mb-3">
                                                                <label for="editName" class="form-label">Name</label>
                                                                <input type="text" name="name" id="editName"
                                                                    class="form-control" required>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label for="editEmail" class="form-label">Email</label>
                                                                <input type="email" name="email" id="editEmail"
                                                                    class="form-control" required>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label for="editPassword" class="form-label">Password
                                                                    <small class="text-danger"
                                                                        style="font-size: 0.8rem;">Kosongkan jika tidak
                                                                        ingin mengubah password.</small>
                                                                </label>
                                                                <input type="text" name="password" id="editPassword"
                                                                    class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Update
                                                                User</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function editUser(userId) {
            const url = `{{ url('users') }}/${userId}/edit`;

            // Fetch data user
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Perbarui action form dengan URL update
                    document.getElementById('editUserForm').action = `{{ url('users') }}/${userId}`;

                    // Isi data ke form
                    document.getElementById('editName').value = data.user.name;
                    document.getElementById('editEmail').value = data.user.email;
                    document.getElementById('editPassword').value = ''; // Kosongkan password

                    // Tampilkan modal
                    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    modal.show();
                })
                .catch(error => console.error('Error:', error));
        }
    </script>

@endsection
