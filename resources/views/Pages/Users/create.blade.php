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

                    <div class="col-md-4">
                        <label for="username" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="Silahkan inputkan nama user" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="ID-card" class="form-label">ID Card / NIK</label>
                        <input type="text" name="ID-card" class="form-control @error('ID-card') is-invalid @enderror"
                            value="{{ old('ID-card') }}" placeholder="Silahkan inputkan ID-card" required>
                        @error('ID-card')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="rfid" class="form-label">RFID</label>
                        <input type="text" name="rfid" class="form-control @error('rfid') is-invalid @enderror"
                            value="{{ old('rfid') }}" placeholder="Silahkan inputkan RFID" required>
                        @error('rfid')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}" placeholder="Silahkan inputkan email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="yourPassword" class="form-label">Password</label>
                        <small class="text-muted">*Default Password value id card</small>
                        <div class="input-group has-validation">
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" id="yourPassword" required
                                readonly>
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
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

                    <div class="col-md-4">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select name="kategori_id" id="kategori_id"
                        class="form-select select2 mb-3 @error('kategori_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('kategori_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->nama_kategori }} - {{ $category->alias_name }}
                            </option>
                        @endforeach
                    </select>


                        @error('kategori_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="departement" class="form-label">Departemen</label>
                        <select name="departement" id="departement"
                            class="form-select mb-3 @error('departement') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih Departemen</option>
                            @foreach ($departements as $departement)
                                <option value="{{ $departement->id }}"
                                    {{ old('departement') == $departement->id ? 'selected' : '' }}>
                                    {{ $departement->deksripsi ?? '-' }}|{{ $departement->nama_departement }}
                                </option>
                            @endforeach
                        </select>

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
        document.addEventListener('DOMContentLoaded', function() {
            const idCardInput = document.querySelector('input[name="ID-card"]');
            const passwordInput = document.getElementById('yourPassword');

            idCardInput.addEventListener('input', function() {
                passwordInput.value = idCardInput.value;
            });

            // Toggle Password Visibility
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
        });
    </script>

    {{-- filter role --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.querySelector('input[name="email"]');
            const roleSelect = document.getElementById('role');
            const departmentSelect = document.getElementById('departement');
            const categorySelect = document.getElementById('kategori_id');



            // Jalankan saat halaman dimuat
            updateFormFields();

            // Event listener untuk perubahan pada role
            roleSelect.addEventListener('change', function() {
                updateFormFields();
            });
        });
    </script>

<script>
    $(document).ready(function() {
        // Pastikan jQuery dan Select2 sudah dimuat
        if (typeof $.fn.select2 !== "undefined") {
            console.log("Select2 Loaded!");

            // Inisialisasi Select2 dengan Search
            $('#kategori_id').select2({
                theme: 'bootstrap-5', // Tetap mengikuti tema Bootstrap
                width: '100%', // Pastikan tetap full width
                placeholder: "Pilih Kategori", // Placeholder di awal dropdown
                allowClear: true // Tambahkan tombol hapus pilihan
            });
        } else {
            console.error("Select2 is not loaded!");
        }
    });
</script>



@endsection
