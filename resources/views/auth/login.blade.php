@extends('layouts.layout-auth.app')
@section('auth-section')
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-white mb-2 uppercase tracking-tight">Selamat Datang</h1>
        <p class="text-slate-400">Masuk untuk mengelola profil dan jadwal latihan Anda.</p>
    </div>

    <form class="space-y-5" action="{{ url('/login/process') }}" method="POST">
        @csrf
        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-widest mb-2 pl-1">Email /
                Username</label>
            <div class="relative">
                <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500"></i>
                <input name="email" value="{{ old('email') }}" type="text" placeholder="Masukkan email atau username"
                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-12 py-3.5 text-white outline-none focus:ring-2 focus:ring-ksc-blue focus:border-transparent transition">
            </div>
            @if(has_error('email'))
                <p class="text-red-500 text-[10px] mt-1.5 ml-1 font-bold italic tracking-wide">⚠ {{ error('email') }}</p>
            @endif
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-widest mb-2 pl-1">Kata Sandi</label>
            <div class="relative">
                <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-500"></i>
                <input name="password" type="password" placeholder="••••••••"
                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-12 py-3.5 text-white outline-none focus:ring-2 focus:ring-ksc-blue focus:border-transparent transition">
            </div>
            @if(has_error('password'))
                <p class="text-red-500 text-[10px] mt-1.5 ml-1 font-bold italic tracking-wide">⚠ {{ error('password') }}</p>
            @endif
        </div>

        <div class="flex items-center justify-between text-xs pb-2">
            <label class="flex items-center gap-2 text-slate-400 cursor-pointer group">
                <input name="checkbox" type="checkbox"
                    class="w-4 h-4 rounded border-white/10 bg-white/5 checked:bg-ksc-blue transition">
                <span class="group-hover:text-white transition">Ingat saya</span>
            </label>
            <a href="/forgot-password" class="text-ksc-accent hover:text-yellow-500 font-bold transition">Lupa kata
                sandi?</a>
        </div>

        <button type="submit"
            class="w-full py-4 bg-ksc-blue hover:bg-ksc-dark text-white rounded-2xl font-bold shadow-xl shadow-ksc-blue/20 transition transform hover:-translate-y-1 block text-center">Masuk
            Sekarang</button>
    </form>

    <div class="mt-8 pt-8 border-t border-white/5 text-center">
        <p class="text-slate-400 text-sm">
            Belum punya akun?
            <a href="/register" class="text-ksc-accent font-bold hover:underline underline-offset-4">Daftar Di Sini</a>
        </p>
    </div>

    <div class="text-center mt-8">
        <a href="/"
            class="text-slate-500 hover:text-white text-sm flex items-center justify-center gap-2 transition group">
            <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition"></i> Kembali ke Beranda
        </a>
    </div>
@endsection