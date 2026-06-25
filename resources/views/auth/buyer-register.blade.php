@extends('layouts.buyer', ['title' => 'Daftar Pembeli'])

@section('content')
<section class="mx-auto grid max-w-5xl gap-6 py-4 md:grid-cols-[1fr_1.05fr] md:items-center md:py-10">
    <div class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-orange-100/70 md:p-8">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-orange-700">CATcha Online</p>
        <h1 class="mt-3 text-3xl font-black leading-tight text-stone-950 md:text-4xl">Buat akun pembeli</h1>
        <p class="mt-3 text-sm leading-6 text-stone-500">Akun ini khusus buyer untuk cart, checkout pickup, dan riwayat pesanan. Role admin/kasir tidak bisa dipilih dari halaman ini.</p>
        <a href="{{ route('buyer.shop.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-full border border-orange-700 px-5 text-sm font-bold text-orange-700 transition hover:bg-orange-50">← Kembali ke Katalog</a>
    </div>

    <div class="rounded-[2rem] bg-white p-5 shadow-sm ring-1 ring-orange-100/70 md:p-8">
        <form method="POST" action="{{ route('buyer.register.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="text-sm font-bold text-stone-700">Nama</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="mt-2 w-full rounded-2xl border-stone-200 px-4 py-3 text-sm shadow-sm focus:border-orange-600 focus:ring-orange-600">
                @error('name')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="text-sm font-bold text-stone-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="mt-2 w-full rounded-2xl border-stone-200 px-4 py-3 text-sm shadow-sm focus:border-orange-600 focus:ring-orange-600">
                @error('email')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="text-sm font-bold text-stone-700">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="mt-2 w-full rounded-2xl border-stone-200 px-4 py-3 text-sm shadow-sm focus:border-orange-600 focus:ring-orange-600">
                @error('password')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="text-sm font-bold text-stone-700">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="mt-2 w-full rounded-2xl border-stone-200 px-4 py-3 text-sm shadow-sm focus:border-orange-600 focus:ring-orange-600">
            </div>

            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-2xl bg-orange-700 px-5 text-sm font-black text-white shadow-sm transition hover:bg-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-600 focus:ring-offset-2">Daftar sebagai Buyer</button>
        </form>

        <div class="mt-5 text-center text-sm font-semibold text-stone-500">
            Sudah punya akun?
            <a href="{{ route('buyer.login') }}" class="font-black text-orange-700 hover:text-orange-800">Masuk</a>
        </div>
    </div>
</section>
@endsection
