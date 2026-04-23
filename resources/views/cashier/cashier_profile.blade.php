@extends('layouts.cashier')

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
                                <i class="bi bi-person-lines-fill me-1"></i> Edit Profile
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">
                                <i class="bi bi-key-fill me-1"></i> Change Password
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-2">
                        <!-- Edit Profile Tab -->
                        <div class="tab-pane fade show active" id="profile-edit">
                            <form action="{{ route('update_cashier_profile') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                                @csrf

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

                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-save me-1"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="profile-change-password">
                            <form action="{{ route('update_cashier_password') }}" method="POST" id="passwordForm">
                                @csrf
                                <div class="row mb-3">
                                    <label for="currentPassword" class="col-md-3 col-form-label">Current Password</label>
                                    <div class="col-md-9 position-relative">
                                        <input name="current_password" 
                                               type="password" 
                                               class="form-control" 
                                               id="currentPassword"
                                               required>
                                        </span>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="newPassword" class="col-md-3 col-form-label">New Password</label>
                                    <div class="col-md-9 position-relative">
                                        <input name="new_password" 
                                               type="password" 
                                               class="form-control" 
                                               id="newPassword"
                                               required>
                                        </span>
                                        <div class="password-strength mt-2">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <label for="renewPassword" class="col-md-3 col-form-label">Confirm Password</label>
                                    <div class="col-md-9 position-relative">
                                        <input name="new_password_confirmation" 
                                               type="password" 
                                               class="form-control" 
                                               id="renewPassword"
                                               required>
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
        
        if (password.length >= 8) strength++;
        if (password.length >= 12) strength++;
        
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
        
        if (/\d/.test(password)) strength++;
        
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        
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