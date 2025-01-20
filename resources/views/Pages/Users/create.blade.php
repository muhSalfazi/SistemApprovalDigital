@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="pagetitle">
        <h1>Create User</h1>

        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Data User</a></li>
                <li class="breadcrumb-item active">Create User</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Create User</h5>

                <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('users.store') }}">
                    @csrf
                    <div class="col-md-6">
                        <label for="username" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="silahkan inputkan nama user" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="ID-card" class="form-label">id-card-NIK</label>
                        <input type="text" name="ID-card" class="form-control @error('ID-card') is-invalid @enderror"
                            value="{{ old('ID-card') }}" placeholder="silahkan inputkan nama ID-card"required>
                        @error('ID-card')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="silahkan inputkan email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="yourPassword" class="form-label">Password</label>
                        <div class="input-group has-validation">
                            <input type="password" name="password"
                                class="form-control  @error('password') is-invalid @enderror" id="yourPassword" required>
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select mb-3 @error('role') is-invalid @enderror"
                            required>
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-label">Departement</label>
                        <select name="departement" id="departement"
                            class="form-select mb-3 @error('departement') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih Departement</option>
                            <option value="HRGA" {{ old('departement') == 'HRGA' ? 'selected' : '' }}>HRGA</option>
                            <option value="FAS" {{ old('departement') == 'FAS' ? 'selected' : '' }}>FAS</option>
                            <option value="PPIC" {{ old('departement') == 'PPIC' ? 'selected' : '' }}>PPIC</option>
                        </select>
                        <small class="text-muted">*Role "approvalManager" dan "viewer" tidak perlu memilih
                            departemen</small>
                        @error('departement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-6">
                        <button class="btn btn-primary" type="submit">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- js hidden+show PW --}}
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('yourPassword');
            const passwordIcon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('bi-eye-slash');
                passwordIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('bi-eye');
                passwordIcon.classList.add('bi-eye-slash');
            }
        });
    </script>
    {{-- filter role --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.querySelector('input[name="email"]');
            const roleSelect = document.getElementById('role');
            const departmentSelect = document.getElementById('departement');

            // Fungsi untuk mengubah status dropdown Departement
            function updateDepartmentStatus(selectedRole = null) {
                const role = selectedRole || roleSelect.value;

                if (role === 'approved' || role === 'viewer') {
                    departmentSelect.disabled = true;
                    departmentSelect.required = false;
                    departmentSelect.value = ""; // Reset nilai
                } else {
                    departmentSelect.disabled = false;
                    departmentSelect.required = true;
                }
            }

            emailInput.addEventListener('blur', function() {
                const email = emailInput.value;

                if (!email) return;

                // Kirim permintaan AJAX untuk memeriksa email
                fetch('{{ route('check-email') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            email
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            // Disable role yang sudah dimiliki user, kecuali superadmin
                            Array.from(roleSelect.options).forEach(option => {
                                if (option.value !== 'superadmin') {
                                    option.disabled = data.roles.includes(option.value);
                                }
                            });

                            // Update status departement berdasarkan role pertama yang dimiliki
                            if (data.roles.length > 0) {
                                updateDepartmentStatus(data.roles[0]); // Role pertama untuk status awal
                            }
                        } else {
                            // Enable semua role jika email tidak ditemukan
                            Array.from(roleSelect.options).forEach(option => {
                                option.disabled = false;
                            });

                            // Reset departement ke default
                            updateDepartmentStatus();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            // Jalankan saat halaman dimuat
            updateDepartmentStatus();

            // Event listener untuk perubahan pada role
            roleSelect.addEventListener('change', function() {
                updateDepartmentStatus();
            });
        });
    </script>

@endsection
