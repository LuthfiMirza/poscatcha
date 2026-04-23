@extends('layouts.admin')

@section('content')
<section class="section profile">
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body pt-3">
                    <!-- Bordered Tabs -->
                    <ul class="nav nav-tabs nav-tabs-bordered mb-4">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-edit">
                                <i class="bi bi-person-lines-fill me-1"></i> Edit Profil
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">
                                <i class="bi bi-key-fill me-1"></i> Ganti Password
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-2">
                        <!-- Edit Profile Tab -->
                        <div class="tab-pane fade show active" id="profile-edit">
                            <form action="{{ route('update_admin_profile') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                                @csrf
                                {{-- <div class="row mb-4 align-items-center">
                                    <label for="profileImage" class="col-md-3 col-form-label">Foto Profil</label>
                                    <div class="col-md-9">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <img src="{{ asset('storage/assets/foto/' . ($user->foto ?? 'profile.jpeg')) }}" 
                                                     alt="Profile" 
                                                     class="border rounded-circle"
                                                     width="100"
                                                     height="100"
                                                     id="profilePreview">
                                            </div>
                                            <div>
                                                <input type="file" 
                                                       name="foto" 
                                                       id="profileImage" 
                                                       class="form-control d-none"
                                                       accept="image/*">
                                                <button type="button" 
                                                        class="btn btn-outline-primary btn-sm me-2"
                                                        onclick="document.getElementById('profileImage').click()">
                                                    <i class="bi bi-upload me-1"></i> Unggah Foto
                                                </button>
                                                @if($user->foto)
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm"
                                                        id="removePhotoBtn">
                                                    <i class="bi bi-trash me-1"></i> Hapus Foto
                                                </button>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted">Format: JPG, PNG (max 2MB)</small>
                                    </div>
                                </div> --}}

                                <div class="row mb-3">
                                    <label for="name" class="col-md-3 col-form-label">Full Name</label>
                                    <div class="col-md-9">
                                        <input name="name" 
                                               type="text" 
                                               class="form-control" 
                                               id="name" 
                                               value="{{ old('name', $user->name) }}" 
                                               required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="email" class="col-md-3 col-form-label">Email</label>
                                    <div class="col-md-9">
                                        <input name="email" 
                                               type="email" 
                                               class="form-control" 
                                               id="email" 
                                               value="{{ old('email', $user->email) }}" 
                                               required>
                                    </div>
                                </div>

                                {{-- <div class="row mb-3">
                                    <label for="notelp" class="col-md-3 col-form-label">No Telepon</label>
                                    <div class="col-md-9">
                                        <input name="notelp" 
                                               type="tel" 
                                               class="form-control" 
                                               id="notelp" 
                                               value="{{ old('notelp', $user->notelp) }}" 
                                               required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="kota" class="col-md-3 col-form-label">Kota</label>
                                    <div class="col-md-9">
                                        <select name="kota" class="form-select" id="kota" required>
                                            <option value="Jakarta Utara" {{ old('kota', $user->kota) == 'Jakarta Utara' ? 'selected' : '' }}>Jakarta Utara</option>
                                            <option value="Jakarta Timur" {{ old('kota', $user->kota) == 'Jakarta Timur' ? 'selected' : '' }}>Jakarta Timur</option>
                                            <option value="Jakarta Barat" {{ old('kota', $user->kota) == 'Jakarta Barat' ? 'selected' : '' }}>Jakarta Barat</option>
                                            <option value="Jakarta Pusat" {{ old('kota', $user->kota) == 'Jakarta Pusat' ? 'selected' : '' }}>Jakarta Pusat</option>
                                            <option value="Jakarta Selatan" {{ old('kota', $user->kota) == 'Jakarta Selatan' ? 'selected' : '' }}>Jakarta Selatan</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="alamat" class="col-md-3 col-form-label">Alamat Lengkap</label>
                                    <div class="col-md-9">
                                        <textarea name="alamat" 
                                                  class="form-control" 
                                                  id="alamat" 
                                                  rows="3" 
                                                  required>{{ old('alamat', $user->alamat) }}</textarea>
                                    </div>
                                </div> --}}

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="profile-change-password">
                            <form action="{{ route('update_admin_password') }}" method="POST" id="passwordForm">
                                @csrf
                                <div class="row mb-3">
                                    <label for="currentPassword" class="col-md-3 col-form-label">Password Saat Ini</label>
                                    <div class="col-md-9 position-relative">
                                        <input name="current_password" 
                                               type="password" 
                                               class="form-control" 
                                               id="currentPassword"
                                               required>
                                        {{-- <span class="position-absolute end-0 top-50 translate-middle-y me-3 toggle-password" 
                                              style="cursor: pointer;">
                                            <i class="bi bi-eye-fill"></i> --}}
                                        </span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="newPassword" class="col-md-3 col-form-label">Password Baru</label>
                                    <div class="col-md-9 position-relative">
                                        <input name="new_password" 
                                               type="password" 
                                               class="form-control" 
                                               id="newPassword"
                                               required>
                                        {{-- <span class="position-absolute end-0 top-50 translate-middle-y me-3 toggle-password" 
                                              style="cursor: pointer;">
                                            <i class="bi bi-eye-fill"></i> --}}
                                        </span>
                                        <div class="password-strength mt-2">
                                            {{-- <div class="progress" style="height: 5px;">
                                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                            </div> --}}
                                            {{-- <small class="text-muted">Kekuatan password: <span class="strength-text">Lemah</span></small> --}}
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="renewPassword" class="col-md-3 col-form-label">Konfirmasi Password</label>
                                    <div class="col-md-9 position-relative">
                                        <input name="new_password_confirmation" 
                                               type="password" 
                                               class="form-control" 
                                               id="renewPassword"
                                               required>
                                        {{-- <span class="position-absolute end-0 top-50 translate-middle-y me-3 toggle-password" 
                                              style="cursor: pointer;">
                                            <i class="bi bi-eye-fill"></i>
                                        </span> --}}
                                        <div id="passwordMatch" class="mt-2 small"></div>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-key me-1"></i> Ganti Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    /* Custom styling for profile page */
    .nav-tabs-bordered {
        border-bottom: 2px solid #dee2e6;
    }
    
    .nav-tabs-bordered .nav-link {
        padding: 12px 20px;
        font-weight: 600;
        color: #495057;
        border: none;
        border-bottom: 3px solid transparent;
    }
    
    .nav-tabs-bordered .nav-link.active {
        color: #4154f1;
        border-bottom-color: #4154f1;
        background-color: transparent;
    }
    
    .nav-tabs-bordered .nav-link:hover:not(.active) {
        border-bottom-color: #dee2e6;
    }
    
    /* Profile image styling */
    #profilePreview {
        object-fit: cover;
        border: 3px solid #f0f0f0;
    }
    
    /* Password strength indicator */
    .password-strength .progress {
        background-color: #e9ecef;
    }
    
    .password-strength .progress-bar {
        transition: width 0.3s ease;
    }
    
    /* Toggle password visibility */
    .toggle-password {
        color: #6c757d;
    }
    
    .toggle-password:hover {
        color: #4154f1;
    }
    
    /* Form validation feedback */
    .is-invalid ~ .invalid-feedback {
        display: block;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .col-md-3.col-form-label {
            text-align: left;
            margin-bottom: 0.5rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview profile image before upload
    document.getElementById('profileImage').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('profilePreview').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Remove profile photo
    document.getElementById('removePhotoBtn')?.addEventListener('click', function() {
        if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
            document.getElementById('profilePreview').src = "{{ asset('storage/assets/foto/default-profile.jpg') }}";
            // You may want to add a hidden input to indicate photo removal
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_photo';
            input.value = '1';
            document.getElementById('profileForm').appendChild(input);
        }
    });
    
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(element) {
        element.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
            }
        });
    });
    
    // Password strength indicator
    const newPassword = document.getElementById('newPassword');
    if (newPassword) {
        newPassword.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            const progressBar = this.parentElement.querySelector('.progress-bar');
            const strengthText = this.parentElement.querySelector('.strength-text');
            
            progressBar.style.width = strength.percentage + '%';
            progressBar.className = 'progress-bar bg-' + strength.color;
            strengthText.textContent = strength.text;
            strengthText.className = 'strength-text text-' + strength.color;
        });
    }
    
    // Password confirmation check
    const renewPassword = document.getElementById('renewPassword');
    if (renewPassword) {
        renewPassword.addEventListener('input', function() {
            const matchDiv = document.getElementById('passwordMatch');
            if (this.value === newPassword.value) {
                matchDiv.innerHTML = '<i class="bi bi-check-circle-fill text-success me-1"></i>Password cocok';
            } else {
                matchDiv.innerHTML = '<i class="bi bi-exclamation-circle-fill text-danger me-1"></i>Password tidak cocok';
            }
        });
    }
    
    // Check password strength
    function checkPasswordStrength(password) {
        let strength = 0;
        
        // Check length
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        
        // Check for mixed case
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        
        // Check for numbers
        if (/\d/.test(password)) strength++;
        
        // Check for special chars
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        
        // Determine strength level
        if (strength <= 2) {
            return { percentage: 33, color: 'danger', text: 'Lemah' };
        } else if (strength <= 4) {
            return { percentage: 66, color: 'warning', text: 'Sedang' };
        } else {
            return { percentage: 100, color: 'success', text: 'Kuat' };
        }
    }
});
</script>
@endsection