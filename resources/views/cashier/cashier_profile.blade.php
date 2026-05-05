@extends('layouts.cashier')

@section('styles')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      background: #f8f8f8;
      font-family: "Inter", sans-serif;
    }

    #main.main {
      margin-top: 56px !important;
      margin-left: 60px !important;
      padding: 0 !important;
      min-height: calc(100vh - 56px);
      background: #f8f8f8;
    }

    .profile-page {
      min-height: calc(100vh - 56px);
      padding: 32px;
    }

    /* ── Page heading ── */
    .profile-heading {
      margin-bottom: 28px;
    }

    .profile-heading__title {
      margin: 0;
      color: #111827;
      font-size: 20px;
      font-weight: 600;
      line-height: 1.2;
    }

    .profile-heading__sub {
      margin-top: 4px;
      color: #9ca3af;
      font-size: 12px;
    }

    /* ── Avatar card ── */
    .profile-avatar-card {
      display: flex;
      align-items: center;
      gap: 20px;
      padding: 24px;
      border: 1px solid #f0f0f0;
      border-radius: 16px;
      background: #ffffff;
      margin-bottom: 20px;
    }

    .profile-avatar {
      width: 64px;
      height: 64px;
      border-radius: 999px;
      background: #e8650a;
      color: #ffffff;
      font-size: 24px;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 64px;
      letter-spacing: -0.5px;
    }

    .profile-avatar-info__name {
      color: #111827;
      font-size: 15px;
      font-weight: 600;
      line-height: 1.3;
    }

    .profile-avatar-info__role {
      margin-top: 3px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 2px 10px;
      border-radius: 999px;
      background: #fff7ed;
      color: #e8650a;
      font-size: 11px;
      font-weight: 600;
    }

    .profile-avatar-info__email {
      margin-top: 6px;
      color: #9ca3af;
      font-size: 12px;
    }

    /* ── Tabs ── */
    .profile-tabs {
      display: flex;
      gap: 4px;
      padding: 4px;
      border-radius: 12px;
      background: #f0f0f0;
      width: fit-content;
      margin-bottom: 20px;
    }

    .profile-tab-btn {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      padding: 8px 18px;
      border: 0;
      border-radius: 9px;
      background: transparent;
      color: #6b7280;
      font-size: 13px;
      font-weight: 500;
      font-family: "Inter", sans-serif;
      cursor: pointer;
      transition: background-color 140ms ease, color 140ms ease;
    }

    .profile-tab-btn.is-active {
      background: #ffffff;
      color: #111827;
      font-weight: 600;
      box-shadow: 0 1px 3px rgba(0,0,0,.08);
    }

    .profile-tab-btn svg {
      width: 15px;
      height: 15px;
      flex: 0 0 15px;
    }

    /* ── Tab panels ── */
    .profile-panel {
      display: none;
    }

    .profile-panel.is-active {
      display: block;
    }

    /* ── Form card ── */
    .profile-card {
      padding: 28px;
      border: 1px solid #f0f0f0;
      border-radius: 16px;
      background: #ffffff;
      max-width: 560px;
    }

    .profile-card__title {
      margin: 0 0 20px;
      color: #111827;
      font-size: 14px;
      font-weight: 600;
    }

    /* ── Field ── */
    .profile-field {
      margin-bottom: 16px;
    }

    .profile-field:last-of-type {
      margin-bottom: 0;
    }

    .profile-field label {
      display: block;
      margin-bottom: 6px;
      color: #374151;
      font-size: 12px;
      font-weight: 500;
    }

    .profile-field .form-control {
      min-height: 44px;
      border: 1px solid #f0f0f0;
      border-radius: 12px;
      background: #f8f8f8;
      color: #111827;
      font-size: 13px;
      padding: 10px 14px;
      box-shadow: none;
      font-family: "Inter", sans-serif;
      width: 100%;
    }

    .profile-field .form-control:focus {
      border-color: #e8650a;
      background: #fff7ed;
      box-shadow: none;
      outline: none;
    }

    /* password wrapper */
    .profile-field__pw {
      position: relative;
    }

    .profile-field__pw .form-control {
      padding-right: 44px;
    }

    .profile-toggle-pw {
      position: absolute;
      top: 50%;
      right: 14px;
      transform: translateY(-50%);
      background: transparent;
      border: 0;
      padding: 0;
      color: #9ca3af;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .profile-toggle-pw:hover {
      color: #e8650a;
    }

    .profile-toggle-pw svg {
      width: 16px;
      height: 16px;
    }

    /* ── Strength bar ── */
    .profile-strength {
      margin-top: 8px;
    }

    .profile-strength__bar {
      height: 4px;
      border-radius: 999px;
      background: #f0f0f0;
      overflow: hidden;
    }

    .profile-strength__fill {
      height: 100%;
      width: 0;
      border-radius: 999px;
      transition: width 250ms ease, background-color 250ms ease;
    }

    .profile-strength__label {
      margin-top: 5px;
      font-size: 11px;
      color: #9ca3af;
    }

    /* ── Match hint ── */
    .profile-match {
      margin-top: 6px;
      font-size: 11px;
      display: flex;
      align-items: center;
      gap: 5px;
      color: #9ca3af;
    }

    .profile-match svg {
      width: 13px;
      height: 13px;
      flex: 0 0 13px;
    }

    /* ── Divider ── */
    .profile-divider {
      margin: 20px 0;
      border: 0;
      border-top: 1px solid #f0f0f0;
      opacity: 1;
    }

    /* ── Submit btn ── */
    .profile-submit {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      margin-top: 20px;
      padding: 11px 22px;
      border: 1px solid #e8650a;
      border-radius: 12px;
      background: #e8650a;
      color: #ffffff;
      font-size: 13px;
      font-weight: 600;
      font-family: "Inter", sans-serif;
      cursor: pointer;
      transition: background-color 140ms ease, border-color 140ms ease;
    }

    .profile-submit:hover,
    .profile-submit:focus {
      background: #c85508;
      border-color: #c85508;
      color: #ffffff;
    }

    .profile-submit svg {
      width: 15px;
      height: 15px;
    }

    /* ── Flash notices ── */
    .profile-notice {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 12px 16px;
      border-radius: 12px;
      font-size: 13px;
      margin-bottom: 20px;
      max-width: 560px;
    }

    .profile-notice.is-success {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      color: #15803d;
    }

    .profile-notice.is-error {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #dc2626;
    }

    .profile-notice svg {
      width: 16px;
      height: 16px;
      flex: 0 0 16px;
      margin-top: 1px;
    }

    .profile-notice ul {
      margin: 4px 0 0;
      padding-left: 16px;
    }
  </style>
@endsection

@section('content')
<div class="profile-page">

  {{-- Heading --}}
  <div class="profile-heading">
    <h1 class="profile-heading__title">Profil Saya</h1>
    <p class="profile-heading__sub">Kelola informasi akun dan keamanan login kamu</p>
  </div>

  {{-- Flash notices --}}
  @if (session('success'))
    <div class="profile-notice is-success">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
        <path d="M8.5 12.5L11 15L15.5 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <span>{{ session('success') }}</span>
    </div>
  @endif

  @if (session('error'))
    <div class="profile-notice is-error">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
        <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      </svg>
      <span>{{ session('error') }}</span>
    </div>
  @endif

  @if ($errors->any())
    <div class="profile-notice is-error">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
        <path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      </svg>
      <div>
        <div style="font-weight:600;margin-bottom:4px;">Terjadi kesalahan:</div>
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  {{-- Avatar card --}}
  <div class="profile-avatar-card">
    <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
    <div class="profile-avatar-info">
      <div class="profile-avatar-info__name">{{ $user->name }}</div>
      <div class="profile-avatar-info__role">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" style="width:11px;height:11px">
          <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/>
          <path d="M4 19.5C4 16.46 7.58 14 12 14s8 2.46 8 5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
        Cashier
      </div>
      <div class="profile-avatar-info__email">{{ $user->email }}</div>
    </div>
  </div>

  {{-- Tabs --}}
  <div class="profile-tabs" id="profileTabs">
    <button class="profile-tab-btn is-active" data-panel="panel-edit">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M11 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L13 14l-4 1 1-4 8.5-8.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Edit Profil
    </button>
    <button class="profile-tab-btn" data-panel="panel-password">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/>
        <path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <circle cx="12" cy="16" r="1.2" fill="currentColor"/>
      </svg>
      Ganti Password
    </button>
  </div>

  {{-- Panel: Edit Profil --}}
  <div class="profile-panel is-active" id="panel-edit">
    <div class="profile-card">
      <div class="profile-card__title">Informasi Akun</div>
      <form action="{{ route('update_cashier_profile') }}" method="POST">
        @csrf
        <div class="profile-field">
          <label for="edit-name">Nama Lengkap</label>
          <input type="text" name="name" id="edit-name" class="form-control"
                 value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="profile-field">
          <label for="edit-email">Email</label>
          <input type="email" name="email" id="edit-email" class="form-control"
                 value="{{ old('email', $user->email) }}" required>
        </div>
        <hr class="profile-divider">
        <button type="submit" class="profile-submit">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M17 21v-8H7v8M7 3v5h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Simpan Perubahan
        </button>
      </form>
    </div>
  </div>

  {{-- Panel: Ganti Password --}}
  <div class="profile-panel" id="panel-password">
    <div class="profile-card">
      <div class="profile-card__title">Keamanan Akun</div>
      <form action="{{ route('update_cashier_password') }}" method="POST">
        @csrf
        <div class="profile-field">
          <label for="current-pw">Password Saat Ini</label>
          <div class="profile-field__pw">
            <input type="password" name="current_password" id="current-pw" class="form-control" required>
            <button type="button" class="profile-toggle-pw" data-target="current-pw" aria-label="Toggle">
              <svg class="eye-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" stroke="currentColor" stroke-width="1.8"/>
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
              </svg>
              <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="display:none">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M1 1l22 22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="profile-field">
          <label for="new-pw">Password Baru</label>
          <div class="profile-field__pw">
            <input type="password" name="new_password" id="new-pw" class="form-control" required>
            <button type="button" class="profile-toggle-pw" data-target="new-pw" aria-label="Toggle">
              <svg class="eye-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" stroke="currentColor" stroke-width="1.8"/>
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
              </svg>
              <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="display:none">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M1 1l22 22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="profile-strength">
            <div class="profile-strength__bar">
              <div class="profile-strength__fill" id="strength-fill"></div>
            </div>
            <div class="profile-strength__label" id="strength-label"></div>
          </div>
        </div>

        <div class="profile-field">
          <label for="confirm-pw">Konfirmasi Password Baru</label>
          <div class="profile-field__pw">
            <input type="password" name="new_password_confirmation" id="confirm-pw" class="form-control" required>
            <button type="button" class="profile-toggle-pw" data-target="confirm-pw" aria-label="Toggle">
              <svg class="eye-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" stroke="currentColor" stroke-width="1.8"/>
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
              </svg>
              <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true" style="display:none">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M1 1l22 22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="profile-match" id="match-hint" style="display:none">
            <svg id="match-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
              <path d="M8.5 12.5L11 15L15.5 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span id="match-text"></span>
          </div>
        </div>

        <hr class="profile-divider">
        <button type="submit" class="profile-submit">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="5" y="11" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/>
            <path d="M8 11V7a4 4 0 0 1 8 0v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
          Ganti Password
        </button>
      </form>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
(function () {
  // ── Tab switching ──
  const tabs = document.querySelectorAll('.profile-tab-btn');
  tabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      tabs.forEach(function (b) { b.classList.remove('is-active'); });
      document.querySelectorAll('.profile-panel').forEach(function (p) { p.classList.remove('is-active'); });
      btn.classList.add('is-active');
      document.getElementById(btn.dataset.panel).classList.add('is-active');
    });
  });

  // If there are validation errors, try to open the password tab when relevant
  @if ($errors->has('current_password') || $errors->has('new_password') || $errors->has('new_password_confirmation'))
    tabs.forEach(function (b) { b.classList.remove('is-active'); });
    document.querySelectorAll('.profile-panel').forEach(function (p) { p.classList.remove('is-active'); });
    document.querySelector('[data-panel="panel-password"]').classList.add('is-active');
    document.getElementById('panel-password').classList.add('is-active');
  @endif

  // ── Toggle password visibility ──
  document.querySelectorAll('.profile-toggle-pw').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const input = document.getElementById(btn.dataset.target);
      const eyeOn  = btn.querySelector('.eye-icon');
      const eyeOff = btn.querySelector('.eye-off-icon');
      if (input.type === 'password') {
        input.type = 'text';
        eyeOn.style.display  = 'none';
        eyeOff.style.display = '';
      } else {
        input.type = 'password';
        eyeOn.style.display  = '';
        eyeOff.style.display = 'none';
      }
    });
  });

  // ── Password strength ──
  var newPwInput   = document.getElementById('new-pw');
  var strengthFill = document.getElementById('strength-fill');
  var strengthLbl  = document.getElementById('strength-label');

  function calcStrength(pw) {
    var score = 0;
    if (pw.length >= 8)  score++;
    if (pw.length >= 12) score++;
    if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
    if (/\d/.test(pw))   score++;
    if (/[^a-zA-Z0-9]/.test(pw)) score++;
    if (score <= 2) return { pct: 33,  color: '#ef4444', text: 'Lemah' };
    if (score <= 4) return { pct: 66,  color: '#f59e0b', text: 'Sedang' };
    return              { pct: 100, color: '#22c55e', text: 'Kuat' };
  }

  newPwInput.addEventListener('input', function () {
    if (!this.value) {
      strengthFill.style.width = '0';
      strengthLbl.textContent  = '';
      return;
    }
    var s = calcStrength(this.value);
    strengthFill.style.width = s.pct + '%';
    strengthFill.style.backgroundColor = s.color;
    strengthLbl.textContent = s.text;
    strengthLbl.style.color = s.color;
    checkMatch();
  });

  // ── Password match ──
  var confirmInput = document.getElementById('confirm-pw');
  var matchHint    = document.getElementById('match-hint');
  var matchIcon    = document.getElementById('match-icon');
  var matchText    = document.getElementById('match-text');

  function checkMatch() {
    if (!confirmInput.value) { matchHint.style.display = 'none'; return; }
    var ok = confirmInput.value === newPwInput.value;
    matchHint.style.display = 'flex';
    matchHint.style.color   = ok ? '#22c55e' : '#ef4444';
    matchIcon.innerHTML = ok
      ? '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M8.5 12.5L11 15L15.5 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>'
      : '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M9 9l6 6M15 9l-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>';
    matchText.textContent = ok ? 'Password cocok' : 'Password tidak cocok';
  }

  confirmInput.addEventListener('input', checkMatch);
})();
</script>
@endsection
