@extends('layouts.buyer', ['title' => 'Profil Buyer'])

@section('content')
<section class="space-y-5">
    <div class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-orange-100/70 md:p-7">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-700">Profil Buyer</p>
        <h1 class="mt-2 text-2xl font-black text-stone-950 md:text-3xl">Halo, {{ $user->name }}</h1>
        <p class="mt-2 text-sm text-stone-500">Kelola informasi akun pembeli untuk cart dan riwayat pesanan.</p>
        <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold">
            <span class="rounded-full bg-orange-50 px-3 py-2 text-orange-700">{{ $user->email }}</span>
            <span class="rounded-full bg-stone-100 px-3 py-2 text-stone-600">Role: buyer</span>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <div class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-stone-100 md:p-7">
            <h2 class="text-lg font-black text-stone-950">Edit Profil</h2>
            <form method="post" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('patch')
                <div>
                    <label for="name" class="text-sm font-bold text-stone-700">Nama</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autocomplete="name" class="mt-2 w-full rounded-2xl border-stone-200 px-4 py-3 text-sm shadow-sm focus:border-orange-600 focus:ring-orange-600">
                    @error('name')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="text-sm font-bold text-stone-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" class="mt-2 w-full rounded-2xl border-stone-200 px-4 py-3 text-sm shadow-sm focus:border-orange-600 focus:ring-orange-600">
                    @error('email')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
                <button class="inline-flex min-h-11 w-full items-center justify-center rounded-2xl bg-orange-700 px-5 text-sm font-black text-white hover:bg-orange-800" type="submit">Edit Profil</button>
                @if (session('status') === 'profile-updated')
                    <p class="text-sm font-semibold text-green-700">Profil berhasil diperbarui.</p>
                @endif
            </form>
        </div>

        <div class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-stone-100 md:p-7">
            <h2 class="text-lg font-black text-stone-950">Ubah Password</h2>
            <form method="post" action="{{ route('password.update') }}" class="mt-5 space-y-4">
                @csrf
                @method('put')
                <div>
                    <label for="current_password" class="text-sm font-bold text-stone-700">Password Saat Ini</label>
                    <input id="current_password" name="current_password" type="password" autocomplete="current-password" class="mt-2 w-full rounded-2xl border-stone-200 px-4 py-3 text-sm shadow-sm focus:border-orange-600 focus:ring-orange-600">
                    @error('current_password', 'updatePassword')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="text-sm font-bold text-stone-700">Password Baru</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" class="mt-2 w-full rounded-2xl border-stone-200 px-4 py-3 text-sm shadow-sm focus:border-orange-600 focus:ring-orange-600">
                    @error('password', 'updatePassword')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="text-sm font-bold text-stone-700">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="mt-2 w-full rounded-2xl border-stone-200 px-4 py-3 text-sm shadow-sm focus:border-orange-600 focus:ring-orange-600">
                </div>
                <button class="inline-flex min-h-11 w-full items-center justify-center rounded-2xl border border-orange-700 px-5 text-sm font-black text-orange-700 hover:bg-orange-50" type="submit">Ubah Password</button>
                @if (session('status') === 'password-updated')
                    <p class="text-sm font-semibold text-green-700">Password berhasil diperbarui.</p>
                @endif
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-red-100 md:p-7">
        @csrf
        <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-2xl bg-red-50 px-5 text-sm font-black text-red-700 hover:bg-red-100 md:w-auto">Logout</button>
    </form>
</section>
@endsection
