@extends('layouts.layout-auth.app')
@section('auth-section')
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-white mb-2 uppercase tracking-tight">Register</h1>
        <p class="text-slate-400">Lengkapi data diri Anda untuk memulai perjalanan prestasi.</p>
    </div>

    <form class="space-y-6" action="{{ url('/register/process') }}" method="POST">
        @csrf
        
        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-widest mb-2 pl-1">Username</label>
            <input name="username" value="{{ old('username') }}" type="text" placeholder="username123"
                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-white outline-none focus:ring-2 focus:ring-ksc-blue transition @if(has_error('username')) border-red-500 @endif">
            @if(has_error('username'))
                <p class="mt-1 text-xs text-red-500">{{ error('username') }}</p>
            @endif
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-300 uppercase tracking-widest mb-2 pl-1">Alamat Email</label>
            <input name="email" value="{{ old('email') }}" placeholder="email@contoh.com"
                class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-white outline-none focus:ring-2 focus:ring-ksc-blue transition @if(has_error('email')) border-red-500 @endif">
            @if(has_error('email'))
                <p class="mt-1 text-xs text-red-500">{{ error('email') }}</p>
            @endif
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-widest mb-2 pl-1">Kata Sandi</label>
                <input name="password" type="password" placeholder="••••••••"
                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-white outline-none focus:ring-2 focus:ring-ksc-blue transition @if(has_error('password')) border-red-500 @endif">
                @if(has_error('password'))
                    <p class="mt-1 text-xs text-red-500">{{ error('password') }}</p>
                @endif
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase tracking-widest mb-2 pl-1">Konfirmasi Sandi</label>
                <input name="password_confirm" type="password" placeholder="••••••••"
                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-5 py-3.5 text-white outline-none focus:ring-2 focus:ring-ksc-blue transition">
            </div>
        </div>

        <div class="flex items-start gap-3 py-2">
            <input name="terms" type="checkbox" id="terms" required
                class="mt-1 w-5 h-5 rounded border-white/10 bg-white/5 checked:bg-ksc-blue transition cursor-pointer">
            <label for="terms" class="text-sm text-slate-400 cursor-pointer hover:text-white transition">
                Saya menyetujui <a href="#" class="text-ksc-accent font-bold hover:underline">Syarat & Ketentuan</a>
                serta Kebijakan Privasi KSC.
            </label>
        </div>

        <button type="submit"
            class="w-full py-5 bg-ksc-blue hover:bg-ksc-dark text-white rounded-[1.5rem] font-bold shadow-2xl shadow-ksc-blue/30 transition transform hover:-translate-y-1 active:scale-[0.98] text-lg">
            Daftar Sekarang
        </button>
    </form>

    <div class="mt-10 pt-10 border-t border-white/5 text-center">
        <p class="text-slate-400">
            Sudah menjadi member?
            <a href="/login" class="text-ksc-accent font-bold hover:underline underline-offset-4">Masuk Di Sini</a>
        </p>
    </div>
@endsection