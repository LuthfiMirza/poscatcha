<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CATcha Online' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[#f8f3ee] text-stone-900 antialiased">
    @php
        $isBuyer = auth()->check() && auth()->user()->hasRole('buyer');
        $loginRedirectUrl = route('buyer.login.required', ['redirect_to' => url()->current()]);
        $cartLoginUrl = route('buyer.login.required', ['redirect_to' => route('buyer.cart.index')]);
        $isCatalogActive = request()->routeIs('buyer.shop.*') || request()->routeIs('buyer.products.*');
    @endphp

    <div class="min-h-screen pb-24 md:pb-0">
        <header class="sticky top-0 z-40 border-b border-orange-100/70 bg-[#f8f3ee]/95 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
                <a href="{{ route('buyer.shop.index') }}" class="flex items-center gap-2">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-orange-700 text-sm font-black text-white shadow-sm">CT</span>
                    <span class="leading-tight">
                        <span class="block text-sm font-extrabold text-stone-950">CATcha</span>
                        <span class="block text-[11px] font-semibold text-orange-700">Online Ordering</span>
                    </span>
                </a>

                <nav class="hidden items-center gap-2 text-sm font-semibold md:flex">
                    <a class="rounded-full px-4 py-2 {{ $isCatalogActive ? 'bg-orange-700 text-white shadow-sm' : 'text-stone-600 hover:bg-white' }}" href="{{ route('buyer.shop.index') }}">Katalog</a>
                    @if ($isBuyer)
                        <a class="rounded-full px-4 py-2 {{ request()->routeIs('buyer.cart.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-stone-600 hover:bg-white' }}" href="{{ route('buyer.cart.index') }}">Cart ({{ $cartCount ?? 0 }})</a>
                        <a class="rounded-full px-4 py-2 {{ request()->routeIs('buyer.orders.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-stone-600 hover:bg-white' }}" href="{{ route('buyer.orders.index') }}">Pesanan</a>
                        <a class="rounded-full px-4 py-2 {{ request()->routeIs('buyer.profile.*') ? 'bg-orange-700 text-white shadow-sm' : 'text-stone-600 hover:bg-white' }}" href="{{ route('buyer.profile.edit') }}">Profil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="rounded-full px-4 py-2 text-stone-600 hover:bg-white" type="submit">Logout</button>
                        </form>
                    @elseif (auth()->check())
                        <a class="rounded-full px-4 py-2 text-stone-600 hover:bg-white" href="{{ auth()->user()->hasRole('admin') ? route('dashboard_admin') : route('selling_product') }}">Dashboard</a>
                    @else
                        <a class="rounded-full px-4 py-2 text-stone-600 hover:bg-white" href="{{ $cartLoginUrl }}">Cart</a>
                        <a class="rounded-full px-4 py-2 text-stone-600 hover:bg-white" href="{{ route('buyer.login') }}">Login</a>
                        <a class="rounded-full bg-orange-700 px-4 py-2 text-white shadow-sm hover:bg-orange-800" href="{{ route('buyer.register') }}">Register</a>
                    @endif
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-4 md:py-6">
            @if (session('success'))
                <div class="mb-4 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-700 shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 shadow-sm">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded-2xl border border-red-100 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 shadow-sm">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    <nav class="fixed inset-x-0 bottom-0 z-50 border-t border-orange-100 bg-white/95 px-3 pb-[max(env(safe-area-inset-bottom),0.5rem)] pt-2 shadow-[0_-10px_30px_rgba(120,53,15,0.08)] backdrop-blur md:hidden">
        <div class="mx-auto grid max-w-md {{ $isBuyer ? 'grid-cols-5' : 'grid-cols-4' }} gap-1 text-[11px] font-bold">
            <a href="{{ route('buyer.shop.index') }}" class="flex flex-col items-center gap-1 rounded-2xl px-2 py-2 {{ $isCatalogActive ? 'bg-orange-50 text-orange-700' : 'text-stone-500' }}">
                <span class="text-lg">⌂</span>
                <span>Home</span>
            </a>
            <a href="{{ route('buyer.products.index') }}" class="flex flex-col items-center gap-1 rounded-2xl px-2 py-2 {{ request()->routeIs('buyer.products.*') ? 'bg-orange-50 text-orange-700' : 'text-stone-500' }}">
                <span class="text-lg">☕</span>
                <span>Katalog</span>
            </a>
            <a href="{{ $isBuyer ? route('buyer.cart.index') : $cartLoginUrl }}" class="flex flex-col items-center gap-1 rounded-2xl px-2 py-2 {{ request()->routeIs('buyer.cart.*') ? 'bg-orange-50 text-orange-700' : 'text-stone-500' }}">
                <span class="relative text-lg">🛒@if(($cartCount ?? 0) > 0)<span class="absolute -right-2 -top-1 h-4 min-w-4 rounded-full bg-orange-700 px-1 text-[9px] leading-4 text-white">{{ $cartCount }}</span>@endif</span>
                <span>Cart</span>
            </a>
            @if ($isBuyer)
                <a href="{{ route('buyer.orders.index') }}" class="flex flex-col items-center gap-1 rounded-2xl px-2 py-2 {{ request()->routeIs('buyer.orders.*') ? 'bg-orange-50 text-orange-700' : 'text-stone-500' }}">
                    <span class="text-lg">☰</span>
                    <span>Orders</span>
                </a>
                <a href="{{ route('buyer.profile.edit') }}" class="flex flex-col items-center gap-1 rounded-2xl px-2 py-2 {{ request()->routeIs('buyer.profile.*') ? 'bg-orange-50 text-orange-700' : 'text-stone-500' }}">
                    <span class="text-lg">☺</span>
                    <span>Profile</span>
                </a>
            @else
                <a href="{{ route('buyer.login') }}" class="flex flex-col items-center gap-1 rounded-2xl px-2 py-2 text-stone-500">
                    <span class="text-lg">☺</span>
                    <span>Login</span>
                </a>
            @endif
        </div>
    </nav>
    @livewireScripts
</body>
</html>
