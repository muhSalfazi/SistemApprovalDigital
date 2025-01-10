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
                    <div class="col-md-12">
                        <label for="username" class="form-label">Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="silahkan inputkan nama user" required>
                        @error('name')
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
                        <select name="role" id="role" class="form-select  mb-3 @error('role') is-invalid @enderror"
                            required>
                            <option value="" disabled selected>Pilih Role</option>
                            <option value="prepared" {{ old('role') == 'prepared' ? 'selected' : '' }}>prepared</option>
                            <option value="Check1" {{ old('role') == 'Check1' ? 'selected' : '' }}>Check1</option>
                            <option value="Check2" {{ old('role') == 'Check1' ? 'selected' : '' }}>Check2</option>
                            <option value="Approved" {{ old('role') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="viewer" {{ old('role') == 'viewer' ? 'selected' : '' }}>Viewer</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="role" class="form-label">Departement</label>
                        <select name="departement" id="departement"
                            class="form-select  mb-3 @error('departement') is-invalid @enderror" required>
                            <option value="" disabled selected>Pilih Departement</option>
                            <option value="HRGA" {{ old('departement') == 'HRGA' ? 'selected' : '' }}>HRGA</option>
                            <option value="FAS" {{ old('departement') == 'FAS' ? 'selected' : '' }}>FAS</option>
                            <option value="PPIC" {{ old('departement') == 'PPIC' ? 'selected' : '' }}>PPIC</option>
                            <option value="ALL" {{ old('departement') == 'ALL' ? 'selected' : '' }}>ALL</option>
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

@endsection
