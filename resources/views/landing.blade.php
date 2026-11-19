<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCompost | Revolusi Pengelolaan Sampah Organik</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    colors: {
                        dark: '#0f172a',
                        darker: '#020617',
                        neon: '#10b981',
                    }
                }
            }
        }
    </script>

    <!-- Script Cegah Kedip (FOUC) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            transition: background-color 0.5s ease, color 0.5s ease;
        }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .dark .glass-panel {
            background: rgba(30, 41, 59, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glow-neon { box-shadow: 0 0 30px rgba(16, 185, 129, 0.2); }

        .fade-in-up {
            animation: fadeInUp 1s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        @keyframes fadeInUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .bg-grid {
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(0, 0, 0, 0.03) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(0, 0, 0, 0.03) 1px, transparent 1px);
        }
        .dark .bg-grid {
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        }
    </style>
</head>
<body class="antialiased relative bg-slate-50 text-slate-800 dark:bg-[#020617] dark:text-slate-50 overflow-x-hidden">

    <!-- SAKLAR THEME -->
    <button id="theme-toggle" type="button" class="fixed bottom-8 right-8 z-[100] flex items-center justify-center w-14 h-14 rounded-full bg-white dark:bg-gray-800 text-slate-800 dark:text-yellow-400 shadow-[0_5px_20px_rgba(0,0,0,0.2)] dark:shadow-[0_0_20px_rgba(16,185,129,0.4)] border border-slate-200 dark:border-gray-700 hover:scale-110 transition-all duration-300 group">
        <svg id="theme-toggle-light-icon" class="hidden w-7 h-7 group-hover:rotate-90 transition-transform duration-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
        <svg id="theme-toggle-dark-icon" class="hidden w-7 h-7 group-hover:-rotate-12 transition-transform duration-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
    </button>

    <!-- Background Ornamen -->
    <div class="fixed inset-0 -z-10 bg-grid transition-colors duration-300">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-neon/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-[100px]"></div>
    </div>

    <!-- NAVBAR DENGAN LOGO DAUN MURNI -->
    <nav class="fixed w-full top-0 z-50 glass-panel border-b border-slate-200 dark:border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3 group cursor-pointer">
                <!-- LOGO BARU: Ikon Daun (Leaf) -->
                <div class="relative w-11 h-11 flex items-center justify-center">
                    <div class="absolute inset-0 bg-neon rounded-xl blur opacity-40 group-hover:opacity-70 transition-opacity duration-300"></div>
                    
                    <div class="relative w-full h-full bg-gradient-to-br from-emerald-400 to-green-600 rounded-xl flex items-center justify-center border border-white/20 shadow-lg transform group-hover:scale-105 transition-transform duration-300">
                        <!-- Ikon Daun (Leaf) SVG Murni & Asli -->
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path>
                            <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Teks Logo -->
                <div class="flex flex-col">
                    <span class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-none">
                        Smart<span class="text-neon">Compost</span>
                    </span>
                    <span class="text-[10px] font-mono text-slate-500 dark:text-gray-400 tracking-widest uppercase mt-0.5">IoT Solutions</span>
                </div>
            </div>
            
            <div class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-600 dark:text-gray-300">
                <a href="#tentang" class="hover:text-neon transition-colors">Tentang</a>
                <a href="#fitur" class="hover:text-neon transition-colors">Fitur</a>
                <a href="#cara-kerja" class="hover:text-neon transition-colors">Cara Kerja</a>
                <a href="#galeri" class="hover:text-neon transition-colors">Galeri</a>
                <a href="{{ route('login') }}" class="px-6 py-2.5 rounded-full bg-neon text-white dark:text-dark font-bold hover:bg-emerald-500 transition-all shadow-[0_0_15px_rgba(16,185,129,0.4)] hover:shadow-[0_0_25px_rgba(16,185,129,0.7)] hover:-translate-y-0.5 ml-4">Masuk Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="min-h-screen flex items-center justify-center px-6 pt-24 pb-12">
        <div class="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <div class="fade-in-up" style="animation-delay: 0.2s;">
                    <span class="px-4 py-1.5 rounded-full glass-panel text-xs font-mono font-bold text-neon mb-4 inline-block border border-neon/30 uppercase tracking-widest">
                        ⚡ IoT Based Smart System
                    </span>
                </div>
                <h1 class="text-5xl md:text-6xl font-extrabold leading-[1.1] fade-in-up text-slate-900 dark:text-white" style="animation-delay: 0.4s;">
                    Ubah Sampah Menjadi <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-neon to-emerald-500 dark:to-emerald-400">Pupuk Masa Depan</span>
                </h1>
                <p class="text-lg text-slate-600 dark:text-gray-400 leading-relaxed fade-in-up" style="animation-delay: 0.6s;">
                    SmartCompost adalah purwarupa reaktor pengomposan cerdas yang mengintegrasikan sensor lingkungan, aktuator otomatis, dan sistem monitoring berbasis web secara <i>real-time</i>.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 fade-in-up pt-4" style="animation-delay: 0.8s;">
                    <a href="#fitur" class="px-8 py-4 rounded-xl bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white font-bold text-center hover:bg-slate-50 dark:hover:bg-white/10 transition-all flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pelajari Selengkapnya
                    </a>
                </div>
            </div>

            <!-- VIDEO TERKOTAK -->
            <div class="fade-in-up relative" style="animation-delay: 0.6s;">
                <div class="relative mx-auto w-full max-w-lg lg:max-w-xl bg-black dark:bg-dark border-[6px] border-slate-300 dark:border-gray-800 rounded-2xl shadow-xl dark:shadow-[0_0_50px_rgba(16,185,129,0.15)] overflow-hidden group transition-colors duration-300">
                    <div class="absolute top-2 left-1/2 -translate-x-1/2 w-12 h-1.5 bg-slate-500 dark:bg-gray-700 rounded-full z-20"></div>
                    
                    <video autoplay loop muted playsinline class="w-full h-auto max-h-[400px] lg:max-h-[500px] object-contain bg-black transition-opacity duration-500">
                        <source src="{{ asset('kompos.mp4') }}" type="video/mp4">
                    </video>
                    
                    <div class="absolute inset-0 border border-black/5 dark:border-white/10 rounded-xl pointer-events-none z-10"></div>
                    <div class="absolute bottom-4 left-4 z-20 bg-white/80 dark:bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-md border border-slate-200 dark:border-white/10 flex items-center gap-2">
                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                        <span class="text-xs font-mono text-slate-800 dark:text-white tracking-widest">LIVE DEMO</span>
                    </div>
                </div>
                <div class="absolute -bottom-6 left-1/2 -translate-x-1/2 w-3/4 h-4 bg-neon/20 blur-2xl rounded-[100%]"></div>
            </div>
        </div>
    </section>

    <!-- SECTION: TENTANG -->
    <section id="tentang" class="py-20 px-6">
        <div class="max-w-5xl mx-auto text-center space-y-6">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">Apa itu SmartCompost?</h2>
            <p class="text-slate-600 dark:text-gray-400 leading-relaxed text-lg">
                SmartCompost adalah inovasi alat pengolah sampah organik rumah tangga yang dirancang untuk mempercepat proses pengomposan. Dengan menggabungkan mekanisme pencacahan fisik dan kontrol lingkungan otomatis (suhu dan kelembapan), alat ini memastikan bakteri pengurai bekerja dalam kondisi optimal.
            </p>
        </div>
    </section>

    <!-- SECTION: FITUR UTAMA -->
    <section id="fitur" class="py-20 px-6 relative z-10 bg-white/50 dark:bg-darker/50 border-y border-slate-200 dark:border-white/5 transition-colors duration-300">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold mb-4 text-slate-900 dark:text-white">Tiga Pilar Utama</h2>
                <p class="text-slate-600 dark:text-gray-400 max-w-2xl mx-auto">Sistem terintegrasi yang dirancang untuk keandalan, keamanan, dan kemudahan pengelolaan sampah organik.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-panel p-8 rounded-2xl hover:border-neon/50 transition-all duration-300 group hover:-translate-y-2 shadow-sm">
                    <div class="w-14 h-14 bg-blue-500/10 dark:bg-blue-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900 dark:text-white">Monitoring Real-time</h3>
                    <p class="text-slate-600 dark:text-gray-400 leading-relaxed text-sm">Pantau parameter lingkungan kompos (Suhu DS18B20, Kelembapan Tanah, dan pH) secara langsung dengan latensi rendah melalui MQTT.</p>
                </div>

                <div class="glass-panel p-8 rounded-2xl hover:border-neon/50 transition-all duration-300 group border-t-4 border-t-neon hover:-translate-y-2 shadow-sm">
                    <div class="w-14 h-14 bg-neon/10 dark:bg-neon/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-neon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900 dark:text-white">Kendali Pintar</h3>
                    <p class="text-slate-600 dark:text-gray-400 leading-relaxed text-sm">Sistem interlock cerdas yang mengatur aktuator (Kipas & Heater) secara otomatis berdasarkan threshold suhu untuk mencegah overheat.</p>
                </div>

                <div class="glass-panel p-8 rounded-2xl hover:border-neon/50 transition-all duration-300 group hover:-translate-y-2 shadow-sm">
                    <div class="w-14 h-14 bg-purple-500/10 dark:bg-purple-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-purple-500 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900 dark:text-white">Keamanan Presisi</h3>
                    <p class="text-slate-600 dark:text-gray-400 leading-relaxed text-sm">Dilengkapi fitur pengaman Limit Switch pada pintu reaktor. Motor pencacah otomatis terputus jika pintu tidak sengaja terbuka.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: CARA KERJA -->
    <section id="cara-kerja" class="py-20 px-6">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-slate-900 dark:text-white">Bagaimana Sistem Bekerja?</h2>
                <p class="text-slate-600 dark:text-gray-400">Alur proses dari sampah organik hingga menjadi kompos siap pakai.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center relative">
                    <div class="w-16 h-16 mx-auto bg-white dark:bg-dark border-2 border-neon rounded-full flex items-center justify-center text-2xl font-bold text-neon mb-4 glow-neon z-10 relative">1</div>
                    <h4 class="font-bold text-slate-900 dark:text-white mb-2">Input Sampah</h4>
                    <p class="text-sm text-slate-600 dark:text-gray-400">Sampah organik dimasukkan ke dalam reaktor melalui pintu atas.</p>
                </div>
                <div class="text-center relative">
                    <div class="w-16 h-16 mx-auto bg-white dark:bg-dark border-2 border-neon rounded-full flex items-center justify-center text-2xl font-bold text-neon mb-4 glow-neon z-10 relative">2</div>
                    <h4 class="font-bold text-slate-900 dark:text-white mb-2">Pencacahan</h4>
                    <p class="text-sm text-slate-600 dark:text-gray-400">Motor AC 220V memutar pisau 6-mata untuk menghancurkan sampah.</p>
                </div>
                <div class="text-center relative">
                    <div class="w-16 h-16 mx-auto bg-white dark:bg-dark border-2 border-neon rounded-full flex items-center justify-center text-2xl font-bold text-neon mb-4 glow-neon z-10 relative">3</div>
                    <h4 class="font-bold text-slate-900 dark:text-white mb-2">Fermentasi</h4>
                    <p class="text-sm text-slate-600 dark:text-gray-400">Heater & Kipas menjaga suhu dan kelembapan agar bakteri pengurai aktif.</p>
                </div>
                <div class="text-center relative">
                    <div class="w-16 h-16 mx-auto bg-neon border-2 border-neon rounded-full flex items-center justify-center text-2xl font-bold text-dark mb-4 shadow-[0_0_20px_rgba(16,185,129,0.6)] z-10 relative">✓</div>
                    <h4 class="font-bold text-slate-900 dark:text-white mb-2">Panen</h4>
                    <p class="text-sm text-slate-600 dark:text-gray-400">Sistem mendeteksi pH netral (6.5-8.0) & memberi notifikasi siap panen.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: GALERI (ZIG-ZAG) -->
    <section id="galeri" class="py-20 px-6 relative z-10 bg-white/50 dark:bg-darker/50 border-y border-slate-200 dark:border-white/5 transition-colors duration-300 overflow-hidden">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-slate-900 dark:text-white">Dokumentasi Proyek</h2>
                <p class="text-slate-600 dark:text-gray-400">Galeri perancangan perangkat keras dan antarmuka sistem SmartCompost.</p>
            </div>

            <div class="space-y-20">
                <div class="flex flex-col md:flex-row items-center gap-8 md:gap-16">
                    <div class="w-full md:w-1/2 relative group">
                        <div class="absolute -inset-2 bg-neon/20 rounded-3xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <img src="https://images.unsplash.com/photo-1585314638610-4c2b986d0e6e?q=80&w=800&auto=format&fit=crop" alt="Desain 3D" class="relative rounded-3xl shadow-xl w-full h-[300px] md:h-[400px] object-cover border border-slate-200 dark:border-gray-700 transition-transform duration-500 group-hover:scale-[1.02]">
                    </div>
                    <div class="w-full md:w-1/2 space-y-4">
                        <span class="px-3 py-1 bg-neon/10 text-neon text-xs font-bold rounded-full border border-neon/20 uppercase tracking-wider">Hardware Design</span>
                        <h3 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">Desain 3D Reaktor SmartCompost</h3>
                        <p class="text-slate-600 dark:text-gray-400 leading-relaxed">Perancangan bentuk fisik alat menggunakan software SolidWorks. Desain ini memastikan tata letak motor, pisau pencacah, sensor, dan ruang pengomposan tertata dengan presisi dan aman.</p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row-reverse items-center gap-8 md:gap-16">
                    <div class="w-full md:w-1/2 relative group">
                        <div class="absolute -inset-2 bg-blue-500/20 rounded-3xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <img src="https://images.unsplash.com/photo-1558449028-b53a39d100fc?q=80&w=800&auto=format&fit=crop" alt="Skematik Kelistrikan" class="relative rounded-3xl shadow-xl w-full h-[300px] md:h-[400px] object-cover border border-slate-200 dark:border-gray-700 transition-transform duration-500 group-hover:scale-[1.02]">
                    </div>
                    <div class="w-full md:w-1/2 space-y-4">
                        <span class="px-3 py-1 bg-blue-500/10 text-blue-500 text-xs font-bold rounded-full border border-blue-500/20 uppercase tracking-wider">Wiring Diagram</span>
                        <h3 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">Skematik Sistem Kelistrikan</h3>
                        <p class="text-slate-600 dark:text-gray-400 leading-relaxed">Diagram pengkabelan yang menghubungkan Panel Surya, SCC, Baterai VRLA, Inverter, ESP32, Modul Relay, serta seluruh sensor dan aktuator agar bekerja secara harmonis.</p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-8 md:gap-16">
                    <div class="w-full md:w-1/2 relative group">
                        <div class="absolute -inset-2 bg-purple-500/20 rounded-3xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?q=80&w=800&auto=format&fit=crop" alt="Implementasi IoT" class="relative rounded-3xl shadow-xl w-full h-[300px] md:h-[400px] object-cover border border-slate-200 dark:border-gray-700 transition-transform duration-500 group-hover:scale-[1.02]">
                    </div>
                    <div class="w-full md:w-1/2 space-y-4">
                        <span class="px-3 py-1 bg-purple-500/10 text-purple-500 text-xs font-bold rounded-full border border-purple-500/20 uppercase tracking-wider">IoT System</span>
                        <h3 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">Implementasi ESP32 & Sensor</h3>
                        <p class="text-slate-600 dark:text-gray-400 leading-relaxed">Mikrokontroler ESP32 membaca data dari sensor Suhu DS18B20, Kelembapan Tanah, dan pH, lalu mengirimkannya ke cloud via protokol MQTT untuk dipantau secara <i>real-time</i>.</p>
                    </div>
                </div>
            </div>
            
            <p class="text-center text-xs text-slate-500 mt-16 italic">*Gambar di atas hanya ilustrasi. Anda dapat menggantinya dengan foto asli alat Anda di folder public Laravel.</p>
        </div>
    </section>

    <!-- SECTION: TEKNOLOGI -->
    <section id="teknologi" class="py-16 border-t border-slate-200 dark:border-white/10 bg-white/50 dark:bg-black/40 backdrop-blur-sm transition-colors duration-300">
        <div class="max-w-5xl mx-auto px-6 text-center">
            <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-8">Didukung Oleh Teknologi Modern</p>
            <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-60 hover:opacity-100 transition-all duration-500 grayscale hover:grayscale-0">
                <div class="flex flex-col items-center gap-2">
                    <span class="text-2xl font-bold text-slate-800 dark:text-white">Laravel 11</span>
                    <span class="text-[10px] text-slate-500 dark:text-gray-400">Backend API</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <span class="text-2xl font-bold text-slate-800 dark:text-white">ESP32</span>
                    <span class="text-[10px] text-slate-500 dark:text-gray-400">Mikrokontroler</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <span class="text-2xl font-bold text-slate-800 dark:text-white">Supabase</span>
                    <span class="text-[10px] text-slate-500 dark:text-gray-400">Cloud Database</span>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <span class="text-2xl font-bold text-slate-800 dark:text-white">MQTT</span>
                    <span class="text-[10px] text-slate-500 dark:text-gray-400">Protokol IoT</span>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER DENGAN LOGO KECIL -->
    <footer class="py-8 border-t border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-dark transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-green-600 rounded-lg flex items-center justify-center shadow-md">
                    <!-- Ikon Daun Kecil di Footer -->
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path>
                    </svg>
                </div>
                <span class="text-lg font-bold text-slate-800 dark:text-white">SmartCompost</span>
            </div>
            
            <p class="text-sm text-slate-500 dark:text-gray-500 font-mono text-center md:text-left">&copy; 2026 SmartCompost Project. IT Del.</p>
            <p class="text-xs text-slate-400 dark:text-gray-600">Dikembangkan untuk Tugas Akhir D3.</p>
        </div>
    </footer>

    <!-- SCRIPT UNTUK TOGGLE THEME -->
    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function() {
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            if (localStorage.getItem('theme')) {
                if (localStorage.getItem('theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
            }
        });
    </script>
</body>
</html>