<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SmartCompost | IoT Control Center</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] },
                    colors: {
                        brand: '#10b981',
                        brandLight: '#34d399',
                        bgLight: '#f8fafc',
                        cardLight: '#ffffff'
                    }
                }
            }
        }
    </script>

    <!-- Script Pencegahan Kedip (FOUC) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            transition: background-color 0.5s ease, color 0.5s ease;
        }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        .dark .glass-panel {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.5);
        }
        
        .glass-panel:hover {
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 10px 40px -10px rgba(16, 185, 129, 0.15);
        }

        .glow-brand { box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3); }

        .pulse-danger { animation: pulse-danger 2s infinite; }
        @keyframes pulse-danger {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .bg-grid {
            background-size: 50px 50px;
            background-image: linear-gradient(to right, rgba(0, 0, 0, 0.02) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(0, 0, 0, 0.02) 1px, transparent 1px);
        }
        .dark .bg-grid {
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        }

        /* Animasi Chat */
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fade-in-up 0.3s ease-out forwards; }
    </style>
</head>
<body class="antialiased min-h-screen relative overflow-x-hidden bg-bgLight text-slate-800 dark:bg-[#020617] dark:text-slate-50">

    <!-- TOMBOL SAKLAR TEMA -->
    <button id="theme-toggle" type="button" class="fixed bottom-8 right-8 z-[100] flex items-center justify-center w-14 h-14 rounded-full bg-white dark:bg-slate-800 text-slate-800 dark:text-yellow-400 shadow-[0_5px_20px_rgba(0,0,0,0.2)] dark:shadow-[0_0_20px_rgba(16,185,129,0.4)] border border-gray-200 dark:border-gray-700 hover:scale-110 transition-all duration-300 group">
        <svg id="theme-toggle-light-icon" class="hidden w-7 h-7 group-hover:rotate-90 transition-transform duration-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
        <svg id="theme-toggle-dark-icon" class="hidden w-7 h-7 group-hover:-rotate-12 transition-transform duration-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
    </button>

    <!-- ========================================== -->
    <!-- FITUR BARU: AI CHATBOT ASSISTANT -->
    <!-- ========================================== -->
    <!-- Tombol Melayang AI -->
    <button id="ai-chat-btn" class="fixed bottom-8 right-28 z-[100] flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-[0_0_20px_rgba(99,102,241,0.5)] hover:scale-110 transition-all duration-300 group">
        <svg class="w-7 h-7 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
    </button>

    <!-- Jendela Chat AI (Struktur Diperbaiki agar bisa di-scroll) -->
    <div id="ai-chat-window" class="fixed bottom-28 right-8 z-[100] w-96 sm:w-[450px] h-[600px] bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 hidden flex flex-col overflow-hidden transition-all duration-300 transform scale-95 opacity-0 origin-bottom-right">
        <!-- Header Chat (Dengan Tombol WhatsApp) -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-4 flex justify-between items-center text-white flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm">SmartCompost AI</h3>
                    <p class="text-[10px] text-indigo-100">Online • Siap Membantu</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- TOMBOL WHATSAPP (Hubungi Pembuat) -->
                <a href="https://wa.me/6282210335302?text=Halo%20Pembuat%20Alat%20SmartCompost,%20saya%20ingin%20bertanya%20mengenai%20sistem%20kompos%20saya." target="_blank" class="flex items-center gap-1 bg-green-500 hover:bg-green-600 px-3 py-1.5 rounded-full text-xs font-bold transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Hubungi
                </a>
                
                <button id="close-ai-chat" class="text-white/70 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Area Pesan (min-h-0 adalah kunci scroll yang benar) -->
        <div id="chat-messages" class="flex-1 min-h-0 p-4 overflow-y-auto space-y-4 bg-slate-50 dark:bg-[#0f172a]">
            <!-- Pesan Sambutan AI -->
            <div class="flex items-start gap-2">
                <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center flex-shrink-0 mt-1">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div class="bg-white dark:bg-slate-800 p-3 rounded-r-xl rounded-bl-xl text-sm text-gray-700 dark:text-gray-300 shadow-sm border border-gray-100 dark:border-gray-700">
                    Halo! Saya asisten AI SmartCompost. Tanyakan apa saja tentang kondisi kompos Anda saat ini. Jika butuh bantuan langsung, klik tombol <b>Hubungi</b> di atas.
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-3 bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <form id="ai-chat-form" class="flex gap-2">
                <input type="text" id="ai-chat-input" placeholder="Tanya AI..." class="flex-1 bg-slate-100 dark:bg-slate-800 border-none rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-white outline-none" autocomplete="off">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white p-2.5 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- BACKGROUND ORNAMEN -->
    <div class="fixed inset-0 -z-20 bg-grid pointer-events-none transition-colors duration-500">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-brand/5 dark:bg-brand/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-500/5 dark:bg-blue-500/10 rounded-full blur-[100px]"></div>
    </div>

    <!-- NAVBAR DENGAN LOGO DAUN -->
    <nav class="glass-panel sticky top-0 z-50 border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-4 group cursor-pointer">
                <!-- LOGO DAUN -->
                <div class="relative w-11 h-11 flex items-center justify-center">
                    <div class="absolute inset-0 bg-brand rounded-xl blur opacity-40 group-hover:opacity-70 transition-opacity duration-300"></div>
                    <div class="relative w-full h-full bg-gradient-to-br from-emerald-400 to-green-600 rounded-xl flex items-center justify-center border border-white/20 shadow-lg transform group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path>
                            <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path>
                        </svg>
                    </div>
                </div>
                
                <!-- Teks Logo -->
                <div>
                    <span class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white block leading-none">
                        Smart<span class="text-brand">Compost</span>
                    </span>
                    <span class="text-[10px] font-mono text-gray-500 dark:text-gray-400 tracking-widest uppercase font-bold mt-0.5">Control Center</span>
                </div>
            </div>
            
            <div class="hidden md:flex items-center space-x-8 text-sm font-semibold text-gray-600 dark:text-gray-300">
                <a href="#sensor" class="hover:text-brand transition-colors flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-brand"></div> Monitoring</a>
                <a href="#kontrol" class="hover:text-brand transition-colors flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-brand"></div> Control</a>
                <a href="#grafik" class="hover:text-brand transition-colors flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-brand"></div> Analytics</a>
                
                <div class="w-px h-8 bg-gray-300 dark:bg-gray-700 mx-2"></div>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-500/20 hover:bg-red-500 hover:text-white transition-all text-xs font-bold tracking-widest flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        LOGOUT
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-6 py-10 space-y-10 relative z-10">
        
        <!-- HEADER & STATUS FASE KOMPOS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Welcome Text -->
            <div class="lg:col-span-2 glass-panel rounded-3xl p-8 flex flex-col justify-center relative overflow-hidden group">
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand/5 dark:bg-brand/10 rounded-full blur-3xl group-hover:bg-brand/10 transition-all duration-700"></div>
                <div class="relative z-10">
                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2 tracking-tight">System <span class="text-brand">Overview</span></h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm max-w-lg leading-relaxed">Sistem Monitoring dan Pengendalian Reaktor Kompos Cerdas. Data lingkungan diperbarui secara <i>real-time</i> melalui protokol MQTT.</p>
                </div>
            </div>

            <!-- FASE PENGOMPOSAN -->
            <div id="panel_fase_kompos" class="glass-panel rounded-3xl p-6 border-l-4 border-blue-500 transition-all duration-500 relative overflow-hidden flex flex-col justify-center">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Fase Pengomposan</p>
                        <h3 id="status_fase_kompos" class="text-lg font-bold text-blue-600 dark:text-blue-400">Menunggu Data...</h3>
                        <p id="desc_fase_kompos" class="text-xs text-gray-500 mt-1">Analisis Suhu & Kelembapan</p>
                    </div>
                    <div id="indikator_fase" class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center border border-blue-200 dark:border-blue-500/20">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- PANEL LIMIT SWITCH (KEAMANAN) -->
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
            <div id="panel_limit_switch" class="glass-panel rounded-3xl p-4 border-l-4 border-gray-400 transition-all duration-500 relative overflow-hidden flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div id="indikator_pintu" class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Safety Interlock (Pintu Reaktor)</p>
                        <h3 id="status_limit_switch" class="text-sm font-bold text-gray-600 dark:text-gray-300">Menunggu Data...</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- ALERT PANEN -->
        <div id="panel_panen" class="hidden bg-gradient-to-r from-green-50 to-emerald-100 dark:from-brand/20 dark:to-emerald-900/40 border border-brand/30 text-emerald-900 dark:text-white px-8 py-5 rounded-2xl shadow-[0_10px_30px_rgba(16,185,129,0.1)] flex items-center gap-5 relative overflow-hidden">
            <div class="p-3 bg-brand rounded-full animate-bounce relative z-10 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div class="relative z-10">
                <h3 class="font-bold text-xl text-gray-900 dark:text-white">Kompos Siap Panen!</h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm mt-1">Parameter suhu, kelembapan, dan pH telah mencapai standar ideal secara bersamaan.</p>
            </div>
        </div>

        <!-- SENSOR CARDS -->
        <section id="sensor" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Suhu -->
            <div class="glass-panel rounded-3xl p-6 relative group overflow-hidden transition-all duration-300 hover:-translate-y-1">
                <div class="absolute -right-6 -top-6 opacity-5 group-hover:opacity-10 transition-opacity duration-500">
                    <svg class="w-40 h-40 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a3 3 0 00-3 3v7a3 3 0 006 0V5a3 3 0 00-3-3zM9 5a3 3 0 116 0v7a3 3 0 01-6 0V5z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Suhu Reaktor</p>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span id="val_suhu" class="text-6xl font-extrabold text-gray-900 dark:text-white font-mono tracking-tighter">--</span>
                        <span class="text-xl font-bold text-red-500">°C</span>
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <p id="status_suhu" class="text-xs font-mono text-gray-400 uppercase tracking-wider font-semibold">Status: Menunggu...</p>
                    </div>
                </div>
            </div>

            <!-- Kelembapan -->
            <div class="glass-panel rounded-3xl p-6 relative group overflow-hidden transition-all duration-300 hover:-translate-y-1">
                <div class="absolute -right-6 -top-6 opacity-5 group-hover:opacity-10 transition-opacity duration-500">
                    <svg class="w-40 h-40 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kelembapan</p>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span id="val_kelembapan" class="text-6xl font-extrabold text-gray-900 dark:text-white font-mono tracking-tighter">--</span>
                        <span class="text-xl font-bold text-blue-500">%</span>
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <p id="status_kelembapan" class="text-xs font-mono text-gray-400 uppercase tracking-wider font-semibold">Status: Menunggu...</p>
                    </div>
                </div>
            </div>

            <!-- pH -->
            <div class="glass-panel rounded-3xl p-6 relative group overflow-hidden transition-all duration-300 hover:-translate-y-1">
                <div class="absolute -right-6 -top-6 opacity-5 group-hover:opacity-10 transition-opacity duration-500">
                    <svg class="w-40 h-40 text-purple-500" fill="currentColor" viewBox="0 0 24 24"><path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tingkat Keasaman</p>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-bold text-gray-400 mr-1">pH</span>
                        <span id="val_ph" class="text-6xl font-extrabold text-gray-900 dark:text-white font-mono tracking-tighter">--</span>
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <p id="status_ph" class="text-xs font-mono text-gray-400 uppercase tracking-wider font-semibold">Status: Menunggu...</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- PANEL KENDALI -->
        <section id="kontrol" class="space-y-6 pt-6">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-brand/10 rounded-lg border border-brand/20">
                    <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Actuator <span class="text-brand">Control</span></h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Motor Pencacah -->
                <div id="card_motor" class="glass-panel rounded-3xl p-6 flex flex-col justify-between transition-all duration-300 hover:border-brand/40">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <svg class="w-7 h-7 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <!-- Toggle Switch Custom -->
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="toggle_motor" onclick="handleToggle('motor')" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-brand peer-checked:shadow-[0_4px_10px_rgba(16,185,129,0.4)]"></div>
                        </label>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Motor Pencacah</h3>
                        <p id="desc_motor" class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">Status: <span class="font-bold text-red-500">OFF</span>. Mesin dalam kondisi aman.</p>
                    </div>
                </div>

                <!-- Heater -->
                <div id="card_heater" class="glass-panel rounded-3xl p-6 flex flex-col justify-between transition-all duration-300 hover:border-red-400/40">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path></svg>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="toggle_heater" onclick="handleToggle('heater')" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-red-500 peer-checked:shadow-[0_4px_10px_rgba(239,68,68,0.4)]"></div>
                        </label>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Pemanas (Heater)</h3>
                        <p id="desc_heater" class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">Status: <span class="font-bold text-red-500">OFF</span>. Suhu dalam batas aman.</p>
                    </div>
                </div>

                <!-- Kipas -->
                <div id="card_kipas" class="glass-panel rounded-3xl p-6 flex flex-col justify-between transition-all duration-300 hover:border-blue-400/40">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700">
                            <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="toggle_kipas" onclick="handleToggle('kipas')" class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-200 dark:bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-500 peer-checked:shadow-[0_4px_10px_rgba(59,130,246,0.4)]"></div>
                        </label>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Kipas Pendingin</h3>
                        <p id="desc_kipas" class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed font-medium">Status: <span class="font-bold text-red-500">OFF</span>. Sirkulasi udara normal.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- GRAFIK RIWAYAT -->
        <section id="grafik" class="space-y-6 pt-8 border-t border-gray-200 dark:border-gray-800">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand/10 rounded-lg border border-brand/20">
                        <svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">Historical <span class="text-brand">Analytics</span></h2>
                </div>
                <span class="text-xs font-mono bg-brand/10 text-brand px-3 py-1.5 rounded-lg border border-brand/20 flex items-center gap-2 font-bold">
                    <div class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></div>
                    Last 10 Records
                </span>
            </div>

            <div class="space-y-6">
                <!-- Grafik Suhu -->
                <div class="glass-panel rounded-3xl p-6 h-[320px] flex flex-col relative bg-white dark:bg-slate-900">
                    <div class="flex justify-between items-center mb-6 z-10">
                        <h3 class="font-bold text-sm text-gray-800 dark:text-gray-200 tracking-wide">Grafik Suhu</h3>
                        <span class="text-xs font-mono text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 px-3 py-1 rounded-md border border-red-100 dark:border-red-500/20 font-bold">°C</span>
                    </div>
                    <div class="flex-1 relative">
                        <canvas id="chartSuhu"></canvas>
                    </div>
                </div>

                <!-- Grafik Kelembapan -->
                <div class="glass-panel rounded-3xl p-6 h-[320px] flex flex-col relative bg-white dark:bg-slate-900">
                    <div class="flex justify-between items-center mb-6 z-10">
                        <h3 class="font-bold text-sm text-gray-800 dark:text-gray-200 tracking-wide">Grafik Kelembapan</h3>
                        <span class="text-xs font-mono text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 px-3 py-1 rounded-md border border-blue-100 dark:border-blue-500/20 font-bold">%</span>
                    </div>
                    <div class="flex-1 relative">
                        <canvas id="chartKelembapan"></canvas>
                    </div>
                </div>

                <!-- Grafik pH -->
                <div class="glass-panel rounded-3xl p-6 h-[320px] flex flex-col relative bg-white dark:bg-slate-900">
                    <div class="flex justify-between items-center mb-6 z-10">
                        <h3 class="font-bold text-sm text-gray-800 dark:text-gray-200 tracking-wide">Grafik pH</h3>
                        <span class="text-xs font-mono text-brand bg-brand/5 dark:bg-brand/10 px-3 py-1 rounded-md border border-brand/20 font-bold">Scale 0-14</span>
                    </div>
                    <div class="flex-1 relative">
                        <canvas id="chartPh"></canvas>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="border-t border-gray-200 dark:border-gray-800 mt-12 py-8 text-center relative z-10 bg-white dark:bg-slate-900 transition-colors duration-300">
        <p class="text-xs text-gray-400 dark:text-gray-500 font-mono tracking-widest uppercase font-bold">&copy; {{ date('Y') }} SmartCompost IoT System. Institut Teknologi Del.</p>
    </footer>

    <!-- SCRIPTS -->
    <script>
        // ==========================================
        // 0. LOGIKA TOGGLE TEMA (DARK/LIGHT)
        // ==========================================
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
            updateChartTheme();
        });

        // Setup Axios CSRF Token
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let state = {
            suhu: 0, kelembapan: 0, ph: 0,
            isDoorOpen: false, isOverheat: false, isReady: false
        };

        // ==========================================
        // 1. INISIALISASI CHART.JS (ADAPTIF TEMA)
        // ==========================================
        const historyData = @json($sensorData); 
        
        const labels = historyData.map(item => {
            const date = new Date(item.created_at);
            return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }).reverse();
        
        const suhuData = historyData.map(item => item.suhu).reverse();
        const kelembapanData = historyData.map(item => item.kelembapan).reverse();
        const phData = historyData.map(item => item.ph).reverse();

        let chartSuhu, chartKelembapan, chartPh;

        function getChartOptions() {
            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255,255,255,0.05)' : '#f1f5f9';
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const tooltipBg = isDark ? '#0f172a' : '#ffffff';
            const tooltipText = isDark ? '#f8fafc' : '#1e293b';

            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { 
                        backgroundColor: tooltipBg, 
                        titleColor: tooltipText,
                        bodyColor: tooltipText,
                        borderColor: gridColor,
                        borderWidth: 1,
                        padding: 12, 
                        cornerRadius: 8,
                        displayColors: false,
                        titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                        bodyFont: { family: 'Inter', size: 13 }
                    }
                },
                scales: {
                    x: { 
                        grid: { display: false }, 
                        ticks: { color: textColor, font: { size: 10, family: 'JetBrains Mono' } } 
                    },
                    y: { 
                        grid: { color: gridColor }, 
                        ticks: { color: textColor, font: { size: 10, family: 'JetBrains Mono' } } 
                    }
                },
                elements: {
                    line: { tension: 0.4, borderWidth: 3 },
                    point: { radius: 0, hoverRadius: 6, hitRadius: 10, hoverBorderWidth: 2 }
                },
                interaction: { mode: 'index', intersect: false }
            };
        }

        function initCharts() {
            const opts = getChartOptions();

            // 1. Chart Suhu
            chartSuhu = new Chart(document.getElementById('chartSuhu').getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Suhu (°C)',
                        data: suhuData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.05)',
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        fill: true
                    }]
                },
                options: opts
            });

            // 2. Chart Kelembapan
            chartKelembapan = new Chart(document.getElementById('chartKelembapan').getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Kelembapan (%)',
                        data: kelembapanData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        fill: true
                    }]
                },
                options: opts
            });

            // 3. Chart pH
            chartPh = new Chart(document.getElementById('chartPh').getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'pH',
                        data: phData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        fill: true
                    }]
                },
                options: {
                    ...opts,
                    scales: {
                        ...opts.scales,
                        y: { ...opts.scales.y, min: 0, max: 14 } 
                    }
                }
            });
        }

        function updateChartTheme() {
            if (!chartSuhu) return;
            const newOpts = getChartOptions();
            
            [chartSuhu, chartKelembapan, chartPh].forEach((chart, index) => {
                chart.options.scales.x.ticks.color = newOpts.scales.x.ticks.color;
                chart.options.scales.y.ticks.color = newOpts.scales.y.ticks.color;
                chart.options.scales.y.grid.color = newOpts.scales.y.grid.color;
                chart.options.plugins.tooltip.backgroundColor = newOpts.plugins.tooltip.backgroundColor;
                chart.options.plugins.tooltip.titleColor = newOpts.plugins.tooltip.titleColor;
                chart.options.plugins.tooltip.bodyColor = newOpts.plugins.tooltip.bodyColor;
                chart.update();
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            initCharts();
        });

        // ==========================================
        // 2. KONEKSI MQTT (REAL-TIME)
        // ==========================================
        const mqttHost = "{{ env('MQTT_HOST') }}";
        const mqttPort = "{{ env('MQTT_PORT') }}";
        const mqttUser = "{{ env('MQTT_USERNAME') }}";
        const mqttPass = "{{ env('MQTT_PASSWORD') }}";
        
        const brokerUrl = `wss://${mqttHost}:${mqttPort}/mqtt`;
        const topicTelemetry = 'smartcompost/telemetry';
        const topicStatus = 'smartcompost/status';

        const client = mqtt.connect(brokerUrl, {
            username: mqttUser,
            password: mqttPass,
            clientId: 'web_dashboard_' + Math.random().toString(16).substr(2, 8),
            clean: true
        });

        client.on('connect', () => {
            client.subscribe([topicTelemetry, topicStatus], (err) => {
                if (!err) console.log('📡 MQTT Connected & Subscribed');
            });
        });

        client.on('message', (topic, message) => {
            try {
                const payload = JSON.parse(message.toString());
                
                // --- HANDLE DATA TELEMETRY ---
                if (topic === topicTelemetry) {
                    
                    if (payload.suhu !== undefined) {
                        state.suhu = parseFloat(payload.suhu);
                        document.getElementById('val_suhu').innerText = state.suhu.toFixed(1);
                        
                        let stSuhu = document.getElementById('status_suhu');
                        if (state.suhu > 60) {
                            stSuhu.innerHTML = "<span class='text-red-500 font-bold'>OVERHEAT (>60°C)</span>";
                            state.isOverheat = true;
                        } else if (state.suhu >= 40 && state.suhu <= 60) {
                            stSuhu.innerHTML = "<span class='text-brand font-bold'>Ideal (40-60°C)</span>";
                            state.isOverheat = false;
                        } else {
                            stSuhu.innerText = "Terpantau";
                            state.isOverheat = false;
                        }
                    }

                    if (payload.kelembapan !== undefined) {
                        state.kelembapan = parseFloat(payload.kelembapan);
                        document.getElementById('val_kelembapan').innerText = state.kelembapan.toFixed(1);
                        
                        let stLembap = document.getElementById('status_kelembapan');
                        if (state.kelembapan >= 50 && state.kelembapan <= 60) {
                            stLembap.innerHTML = "<span class='text-brand font-bold'>Stabil (Ideal)</span>";
                        } else {
                            stLembap.innerText = "Terpantau";
                        }
                    }

                    if (payload.ph !== undefined) {
                        state.ph = parseFloat(payload.ph);
                        document.getElementById('val_ph').innerText = state.ph.toFixed(1);
                        
                        let stPh = document.getElementById('status_ph');
                        if (state.ph >= 6.5 && state.ph <= 8.0) {
                            stPh.innerHTML = "<span class='text-brand font-bold'>Normal (Netral)</span>";
                        } else {
                            stPh.innerText = "Terpantau";
                        }
                    }

                    // Panggil fungsi evaluasi
                    updateFaseKompos(); 
                    evaluateSafetyInterlocks();

                    // ==========================================
                    // [PERBAIKAN POIN 2]: UPDATE GRAFIK REAL-TIME
                    // ==========================================
                    const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

                    if(chartSuhu) {
                        chartSuhu.data.labels.push(now);
                        chartSuhu.data.datasets[0].data.push(state.suhu);
                        if(chartSuhu.data.labels.length > 10) { chartSuhu.data.labels.shift(); chartSuhu.data.datasets[0].data.shift(); }
                        chartSuhu.update('none');
                    }
                    if(chartKelembapan) {
                        chartKelembapan.data.labels.push(now);
                        chartKelembapan.data.datasets[0].data.push(state.kelembapan);
                        if(chartKelembapan.data.labels.length > 10) { chartKelembapan.data.labels.shift(); chartKelembapan.data.datasets[0].data.shift(); }
                        chartKelembapan.update('none');
                    }
                    if(chartPh) {
                        chartPh.data.labels.push(now);
                        chartPh.data.datasets[0].data.push(state.ph);
                        if(chartPh.data.labels.length > 10) { chartPh.data.labels.shift(); chartPh.data.datasets[0].data.shift(); }
                        chartPh.update('none');
                    }
                }

                // --- HANDLE DATA STATUS (LIMIT SWITCH) ---
                if (topic === topicStatus && payload.limit_switch !== undefined) {
                    const pnlLimit = document.getElementById('panel_limit_switch');
                    const stLimit = document.getElementById('status_limit_switch');
                    const indikator = document.getElementById('indikator_pintu');
                    
                    if (payload.limit_switch === 'TERBUKA') {
                        state.isDoorOpen = true;
                        pnlLimit.className = 'glass-panel rounded-3xl p-4 border-l-4 border-red-500 transition-all duration-500 relative overflow-hidden flex items-center justify-between pulse-danger';
                        stLimit.innerHTML = "<span class='text-red-500 font-bold'>PINTU TERBUKA</span>";
                        indikator.className = 'w-10 h-10 rounded-xl bg-red-50 dark:bg-red-500/10 flex items-center justify-center border border-red-200 dark:border-red-500/20 pulse-danger';
                        indikator.innerHTML = '<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                    } else {
                        state.isDoorOpen = false;
                        pnlLimit.className = 'glass-panel rounded-3xl p-4 border-l-4 border-brand transition-all duration-500 relative overflow-hidden flex items-center justify-between';
                        stLimit.innerHTML = "<span class='text-brand font-bold'>PINTU TERTUTUP</span>";
                        indikator.className = 'w-10 h-10 rounded-xl bg-brand/10 flex items-center justify-center border border-brand/20';
                        indikator.innerHTML = '<svg class="w-5 h-5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                    }
                    evaluateSafetyInterlocks();
                }

            } catch (e) {
                console.error('Gagal parsing payload MQTT:', e);
            }
        });

        // ==========================================
        // 3. LOGIKA KECERDASAN (FASE & KEAMANAN)
        // ==========================================
        
        function updateFaseKompos() {
            const panelFase = document.getElementById('panel_fase_kompos');
            const statusFase = document.getElementById('status_fase_kompos');
            const descFase = document.getElementById('desc_fase_kompos');
            const indikatorFase = document.getElementById('indikator_fase');
            const panelPanen = document.getElementById('panel_panen');

            if (state.suhu >= 40 && state.suhu <= 60) {
                // Fase Termofilik
                panelFase.className = 'glass-panel rounded-3xl p-6 border-l-4 border-orange-500 transition-all duration-500 relative overflow-hidden flex flex-col justify-center';
                statusFase.innerHTML = "<span class='text-orange-500 font-bold'>Fase Termofilik (Aktif)</span>";
                descFase.innerText = "Bakteri sedang aktif menguraikan sampah. Jaga suhu 40-60°C.";
                indikatorFase.className = 'w-12 h-12 rounded-xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center border border-orange-200 dark:border-orange-500/20';
                indikatorFase.innerHTML = '<svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path></svg>';
                panelPanen.classList.add('hidden');
                
            // [PERBAIKAN POIN 1]: MENAMBAHKAN SYARAT pH
            } else if (state.suhu < 40 && state.suhu > 0 && state.kelembapan >= 50 && state.kelembapan <= 60 && state.ph >= 6.5 && state.ph <= 8.0) {
                // Fase Matang (Siap Panen)
                panelFase.className = 'glass-panel rounded-3xl p-6 border-l-4 border-brand transition-all duration-500 relative overflow-hidden flex flex-col justify-center glow-brand';
                statusFase.innerHTML = "<span class='text-brand font-bold'>Fase Pematangan (Siap Panen)</span>";
                descFase.innerText = "Suhu turun, kelembapan stabil & pH netral. Kompos siap dipanen.";
                indikatorFase.className = 'w-12 h-12 rounded-xl bg-brand/10 flex items-center justify-center border border-brand/20 pulse-danger';
                indikatorFase.innerHTML = '<svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                panelPanen.classList.remove('hidden');

            } else {
                // Fase Penyesuaian
                panelFase.className = 'glass-panel rounded-3xl p-6 border-l-4 border-blue-500 transition-all duration-500 relative overflow-hidden flex flex-col justify-center';
                statusFase.innerHTML = "<span class='text-blue-500 font-bold'>Fase Penyesuaian</span>";
                descFase.innerText = "Menunggu parameter stabil (Suhu ideal / Kelembapan 50-60%).";
                indikatorFase.className = 'w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center border border-blue-200 dark:border-blue-500/20';
                indikatorFase.innerHTML = '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                panelPanen.classList.add('hidden');
            }
        }

        function evaluateSafetyInterlocks() {
            const tMotor = document.getElementById('toggle_motor'); const dMotor = document.getElementById('desc_motor');
            const tHeater = document.getElementById('toggle_heater'); const dHeater = document.getElementById('desc_heater');
            const tKipas = document.getElementById('toggle_kipas'); const dKipas = document.getElementById('desc_kipas');

            if (state.isDoorOpen) {
                tMotor.checked = false; tMotor.disabled = true;
                document.getElementById('card_motor').classList.add('border-red-300', 'bg-red-50', 'dark:bg-red-900/10');
                dMotor.innerHTML = "<span class='text-red-500 font-bold'>TERKUNCI: Pintu terbuka!</span>";
            } else {
                tMotor.disabled = false;
                document.getElementById('card_motor').classList.remove('border-red-300', 'bg-red-50', 'dark:bg-red-900/10');
                if (dMotor.innerText.includes('TERKUNCI')) dMotor.innerHTML = "Status: <span class='font-bold text-red-500'>OFF</span>. Mesin aman.";
            }

            if (state.isOverheat) {
                tHeater.checked = false; tHeater.disabled = true;
                document.getElementById('card_heater').classList.add('border-red-300', 'bg-red-50', 'dark:bg-red-900/10');
                dHeater.innerHTML = "<span class='text-red-500 font-bold'>TERKUNCI: Overheat!</span>";

                tKipas.checked = true; tKipas.disabled = true;
                document.getElementById('card_kipas').classList.add('border-blue-300', 'bg-blue-50', 'dark:bg-blue-900/10');
                dKipas.innerHTML = "<span class='text-blue-500 font-bold'>OTOMATIS: Evakuasi panas.</span>";
            } else {
                tHeater.disabled = false; document.getElementById('card_heater').classList.remove('border-red-300', 'bg-red-50', 'dark:bg-red-900/10');
                if (dHeater.innerText.includes('TERKUNCI')) dHeater.innerHTML = "Status: <span class='font-bold text-red-500'>OFF</span>. Suhu aman.";

                tKipas.disabled = false; document.getElementById('card_kipas').classList.remove('border-blue-300', 'bg-blue-50', 'dark:bg-blue-900/10');
                if (dKipas.innerText.includes('OTOMATIS')) dKipas.innerHTML = "Status: <span class='font-bold text-red-500'>OFF</span>. Sirkulasi normal.";
            }
        }

        // ==========================================
        // 4. FUNGSI TOGGLE (AXIOS)
        // ==========================================
        function handleToggle(device) {
            const cb = document.getElementById('toggle_' + device);
            
            if (device === 'motor' && state.isDoorOpen) { cb.checked = false; return Swal.fire({title: 'Akses Ditolak', text: 'Tutup pintu reaktor terlebih dahulu!', icon: 'error', background: '#ffffff', color: '#1e293b'}); }
            if (device === 'heater' && state.isOverheat) { cb.checked = false; return Swal.fire({title: 'Akses Ditolak', text: 'Suhu sedang Overheat!', icon: 'warning', background: '#ffffff', color: '#1e293b'}); }
            if (device === 'kipas' && state.isOverheat) { cb.checked = true; return Swal.fire({title: 'Akses Ditolak', text: 'Kipas sedang mengevakuasi udara panas!', icon: 'warning', background: '#ffffff', color: '#1e293b'}); }

            const isChecked = cb.checked;
            const isDark = document.documentElement.classList.contains('dark');
            
            Swal.fire({ 
                title: 'Memproses Perintah...', 
                allowOutsideClick: false, 
                background: isDark ? '#0f172a' : '#ffffff', 
                color: isDark ? '#ffffff' : '#1e293b',
                didOpen: () => { Swal.showLoading() } 
            });

            axios.patch('{{ route("actuator.toggle") }}', { 
                device: device, 
                status: isChecked 
            })
            .then(response => {
                Swal.close();
                if (response.data.success) {
                    updateDescStatus(device, isChecked);
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: `Perangkat ${device} berhasil di${isChecked ? 'hidupkan' : 'matikan'}.`,
                        timer: 1500,
                        showConfirmButton: false,
                        background: isDark ? '#0f172a' : '#ffffff',
                        color: isDark ? '#ffffff' : '#1e293b'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                cb.checked = !isChecked;
                const errorMsg = error.response?.data?.message || 'Gagal terhubung ke server.';
                Swal.fire({title: 'Gagal', text: errorMsg, icon: 'error', background: isDark ? '#0f172a' : '#ffffff', color: isDark ? '#ffffff' : '#1e293b'});
            });
        }

        function updateDescStatus(device, isChecked) {
            const el = document.getElementById('desc_' + device);
            const statusColor = isChecked ? 'text-brand' : 'text-red-500';
            const statusText = isChecked ? 'ON' : 'OFF';
            
            if (device === 'motor') el.innerHTML = `Status: <span class="font-bold ${statusColor}">${statusText}</span>. Mesin ${isChecked ? 'berputar mencacah.' : 'aman digunakan.'}`;
            if (device === 'heater') el.innerHTML = `Status: <span class="font-bold ${statusColor}">${statusText}</span>. Pemanas ${isChecked ? 'sedang bekerja.' : 'dalam kondisi aman.'}`;
            if (device === 'kipas') el.innerHTML = `Status: <span class="font-bold ${statusColor}">${statusText}</span>. Sirkulasi ${isChecked ? 'berjalan.' : 'normal.'}`;
        }

        // ==========================================
        // 5. LOGIKA AI CHATBOT (Dengan Status Pintu & Fallback WA)
        // ==========================================
        const aiChatBtn = document.getElementById('ai-chat-btn');
        const aiChatWindow = document.getElementById('ai-chat-window');
        const closeAiChat = document.getElementById('close-ai-chat');
        const aiChatForm = document.getElementById('ai-chat-form');
        const aiChatInput = document.getElementById('ai-chat-input');
        const chatMessages = document.getElementById('chat-messages');

        // Buka/Tutup Jendela Chat
        aiChatBtn.addEventListener('click', () => {
            aiChatWindow.classList.toggle('hidden');
            setTimeout(() => {
                aiChatWindow.classList.toggle('opacity-0');
                aiChatWindow.classList.toggle('scale-95');
            }, 10);
        });

        closeAiChat.addEventListener('click', () => {
            aiChatWindow.classList.add('opacity-0', 'scale-95');
            setTimeout(() => {
                aiChatWindow.classList.add('hidden');
            }, 300);
        });

        // Fungsi Kirim Pesan
        aiChatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const message = aiChatInput.value.trim();
            if (!message) return;

            // 1. Tampilkan pesan user
            appendMessage('user', message);
            aiChatInput.value = '';
            aiChatInput.disabled = true; // Kunci input sementara

            // 2. Tampilkan indikator loading
            const loadingId = 'loading-' + Date.now();
            appendMessage('ai', '<div class="flex gap-1"><div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div><div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div><div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div></div>', loadingId);

            // 3. Kirim ke Backend (Termasuk Status Pintu)
            axios.post('{{ route("ai.chat") }}', {
                message: message,
                suhu: state.suhu || 0,
                kelembapan: state.kelembapan || 0,
                ph: state.ph || 0,
                status_pintu: state.isDoorOpen ? 'TERBUKA' : 'TERTUTUP' // <--- TAMBAHKAN INI
            })
            .then(response => {
                document.getElementById(loadingId)?.remove();
                if (response.data.success) {
                    appendMessage('ai', response.data.reply);
                } else {
                    // Jika status fallback true, redirect ke WA
                    if (response.data.fallback) {
                        appendMessage('ai', '⚠️ Server AI sedang tidak stabil. Mengarahkan Anda ke pembuat alat via WhatsApp...');
                        setTimeout(() => {
                            window.open('https://wa.me/6282210335302?text=Halo%20Pembuat%20Alat%20SmartCompost,%20saya%20ingin%20bertanya%20mengenai%20sistem%20kompos%20saya.', '_blank');
                        }, 2000);
                    } else {
                        appendMessage('ai', '❌ ' + (response.data.message || 'AI sedang sibuk.'));
                    }
                }
            })
            .catch(error => {
                document.getElementById(loadingId)?.remove();
                const msg = error.response?.data?.message || 'Gagal terhubung ke server.';
                
                // Jika error network, redirect ke WA juga
                appendMessage('ai', '⚠️ Koneksi gagal. Mengarahkan ke pembuat alat via WhatsApp...');
                setTimeout(() => {
                    window.open('https://wa.me/6282210335302?text=Halo%20Pembuat%20Alat%20SmartCompost,%20saya%20ingin%20bertanya%20mengenai%20sistem%20kompos%20saya.', '_blank');
                }, 2000);
            })
            .finally(() => {
                aiChatInput.disabled = false; // Buka kunci input kembali
                aiChatInput.focus();
            });
        });

        // Fungsi Helper untuk Menampilkan Bubble Chat
        function appendMessage(sender, text, id = '') {
            const isUser = sender === 'user';
            
            // Bersihkan teks dari karakter yang bisa merusak HTML
            const safeText = text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/\n/g, '<br>');

            const html = `
                <div class="flex items-start gap-2 ${isUser ? 'flex-row-reverse' : ''} animate-fade-in-up">
                    <div class="w-6 h-6 rounded-full ${isUser ? 'bg-emerald-100 dark:bg-emerald-900' : 'bg-indigo-100 dark:bg-indigo-900'} flex items-center justify-center flex-shrink-0 mt-1">
                        ${isUser 
                            ? '<svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>'
                            : '<svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>'
                        }
                    </div>
                    <div id="${id}" class="${isUser ? 'bg-emerald-600 text-white rounded-l-xl rounded-br-xl' : 'bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 rounded-r-xl rounded-bl-xl border border-gray-100 dark:border-gray-700'} p-3 text-sm shadow-sm max-w-[85%] leading-relaxed overflow-hidden break-words">
                        ${safeText}
                    </div>
                </div>
            `;
            chatMessages.insertAdjacentHTML('beforeend', html);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    </script>
</body>
</html>