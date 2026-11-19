<x-guest-layout>
    <!-- Container Utama: Full Screen, Split 2 Kolom -->
    <div class="min-h-screen flex w-full">
        
        <!-- ========================================== -->
        <!-- SISI KIRI: VISUAL & BRANDING (Hanya Desktop) -->
        <!-- ========================================== -->
        <div class="hidden lg:flex lg:w-1/2 relative items-center justify-center overflow-hidden bg-slate-900">
            
            <!-- Video Background -->
            <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover opacity-40">
                <source src="{{ asset('kompos.mp4') }}" type="video/mp4">
            </video>
            
            <!-- Overlay Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/90 via-slate-900/90 to-black/95"></div>
            
            <!-- Ornamen Cahaya -->
            <div class="absolute top-0 left-0 w-96 h-96 bg-emerald-500/20 rounded-full blur-[100px]"></div>

            <!-- Konten Teks (LOGO DIHAPUS) -->
            <div class="relative z-10 px-12 text-center max-w-lg">
                
                <h1 class="text-5xl font-extrabold text-white tracking-tight mb-4">
                    Smart<span class="text-emerald-400">Compost</span>
                </h1>
                <p class="text-emerald-100 text-lg leading-relaxed mb-10">
                    Sistem Monitoring dan Pengendalian Reaktor Kompos Cerdas Berbasis Internet of Things (IoT).
                </p>
                
                <!-- Status Badge -->
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md px-5 py-2.5 rounded-full border border-white/20 text-sm text-white">
                    <div class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse"></div>
                    Sistem Siap Digunakan
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- SISI KANAN: FORM LOGIN -->
        <!-- ========================================== -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-slate-50 relative">
            <div class="w-full max-w-md relative z-10">
                
                <!-- Logo Mobile (Hanya muncul di HP) -->
                <div class="lg:hidden flex items-center justify-center gap-3 mb-10">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path>
                            <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold text-slate-900">SmartCompost</span>
                </div>

                <!-- Judul -->
                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Selamat Datang 👋</h2>
                    <p class="text-slate-500 mt-2">Masuk untuk memantau kondisi reaktor kompos Anda.</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Alamat Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-200 bg-white shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none text-slate-800"
                                placeholder="nama@email.com">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-slate-200 bg-white shadow-sm focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all outline-none text-slate-800"
                                placeholder="••••••••">
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between pt-2">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                            <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-emerald-600 shadow-sm focus:ring-emerald-500 cursor-pointer" name="remember">
                            <span class="ml-2 text-sm text-slate-600 group-hover:text-emerald-600 transition-colors">Ingat Saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm font-semibold text-emerald-600 hover:text-emerald-800 transition-colors" href="{{ route('password.request') }}">
                                Lupa Password?
                            </a>
                        @endif
                    </div>

                    <!-- Tombol Login -->
                    <button type="submit" class="w-full py-4 px-4 mt-4 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold rounded-2xl shadow-[0_8px_30px_rgba(16,185,129,0.3)] hover:shadow-[0_8px_30px_rgba(16,185,129,0.5)] hover:-translate-y-0.5 transition-all duration-300 flex justify-center items-center gap-2">
                        MASUK DASHBOARD
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>

                    <!-- Link Register -->
                    <p class="text-center text-sm text-slate-600 mt-8">
                        Belum punya akun? 
                        <a href="{{ route('register') }}" class="font-bold text-emerald-600 hover:text-emerald-800 transition-colors underline decoration-2 underline-offset-4 decoration-emerald-200 hover:decoration-emerald-600">Daftar Sekarang</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Animasi Kustom -->
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
    </style>
</x-guest-layout>