<?php
session_start();
// include 'config/koneksi.php';
// include 'cek_akses.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>About Me - Bento Portfolio</title>
    <?php include 'layouts/head.php'; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Syntax Highlighting untuk JSON (Tema Gelap pada Kotak Editor) */
        .json-key { color: #9cdcfe; } /* Light Blue */
        .json-string { color: #ce9178; } /* Orange */
        .json-number { color: #b5cea8; } /* Green */
        .json-bracket { color: #ffd700; } /* Yellow */
        
        /* Animasi Glow & Elevate saat di-hover (Light Mode) */
        .bento-box {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .bento-box:hover {
            border-color: #bfdbfe; /* blue-200 */
            box-shadow: 0 20px 40px -15px rgba(37, 99, 235, 0.15);
            transform: translateY(-4px);
        }
        
        /* Aksen Gradien Teks */
        .text-gradient-accent {
            background: linear-gradient(135deg, #2563eb, #6366f1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex">
    
    <!-- Sidebar Tetap Ada & Selaras warnanya -->
    <?php include 'layouts/sidebar.php'; ?>

    <!-- Main Content Area: Background Terang (bg-slate-50) mengikuti sistem -->
    <main class="flex-1 p-4 md:p-8 bg-slate-50 overflow-y-auto w-full relative">
        
        <!-- Ornamen Background Halus -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-100/50 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-10 w-96 h-96 bg-indigo-100/50 rounded-full blur-[100px] pointer-events-none"></div>

        <div class="max-w-6xl mx-auto w-full pt-4 pb-12 relative z-10">
            
            <!-- Floating Navigation -->
            <div class="flex justify-center mb-10">
                <nav class="flex items-center gap-2 bg-white/80 backdrop-blur-md border border-slate-200 p-1.5 rounded-full shadow-sm">
                    <a href="#" class="px-5 py-2 rounded-full bg-blue-600 text-white text-sm font-bold shadow-md">Profil</a>
                    <a href="#" class="px-5 py-2 rounded-full text-slate-500 hover:text-blue-600 hover:bg-blue-50 text-sm font-bold transition">Skill</a>
                    <a href="#" class="px-5 py-2 rounded-full text-slate-500 hover:text-blue-600 hover:bg-blue-50 text-sm font-bold transition">Proyek</a>
                </nav>
            </div>

            <!-- BENTO GRID LAYOUT (Light Theme) -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 auto-rows-min">
                
                <!-- BENTO 1: Hero Intro (Span 8) -->
                <div class="md:col-span-8 bg-white border border-slate-200 rounded-[2rem] p-8 md:p-10 bento-box relative overflow-hidden flex flex-col justify-center shadow-sm">
                    <!-- Pola Grid Transparan di Background -->
                    <div class="absolute inset-0 opacity-[0.03] bg-[linear-gradient(to_right,#000_1px,transparent_1px),linear-gradient(to_bottom,#000_1px,transparent_1px)] bg-[size:24px_24px]"></div>
                    
                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-blue-200 bg-blue-50 text-xs font-bold text-blue-600 mb-6 uppercase tracking-wider">
                            <i class="fa-solid fa-code"></i> Developer in Training
                        </div>
                        
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-slate-900 leading-tight mb-4 tracking-tight">
                            Hi, saya <span class="text-gradient-accent">Rangga</span>.
                        </h1>
                        <p class="text-lg text-slate-500 max-w-xl leading-relaxed mb-8 font-medium">
                            Pelajar yang sangat antusias dengan dunia teknologi. Saya merancang antarmuka yang bersih dan membangun sistem di baliknya.
                        </p>
                        
                        <div class="flex flex-wrap gap-4">
                            <button class="px-6 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition transform hover:-translate-y-0.5">
                                Lihat Portofolio
                            </button>
                            <button class="px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-bold border border-slate-200 hover:bg-slate-200 transition">
                                Hubungi Saya
                            </button>
                        </div>
                    </div>
                </div>

                <!-- BENTO 2: Profile & School (Span 4) -->
                <div class="md:col-span-4 bg-white border border-slate-200 rounded-[2rem] p-8 bento-box flex flex-col items-center justify-center text-center relative group shadow-sm">
                    <!-- Status Indicator -->
                    <div class="absolute top-6 right-6 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </div>

                    <div class="w-32 h-32 rounded-full border-4 border-white bg-slate-100 flex items-center justify-center mb-6 shadow-xl overflow-hidden group-hover:border-blue-200 group-hover:bg-blue-50 transition-colors duration-500 relative">
                        <!-- Latar gradien halus saat hover -->
                        <div class="absolute inset-0 bg-gradient-to-tr from-blue-100 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <i class="fa-solid fa-user-astronaut text-5xl text-slate-400 group-hover:text-blue-600 transition-colors duration-500 relative z-10"></i>
                    </div>
                    
                    <h3 class="text-2xl font-black text-slate-800 mb-1">Rangga R.N</h3>
                    <p class="text-xs font-bold text-slate-400 mb-5 tracking-widest uppercase">Pelajar Aktif</p>
                    
                    <div class="w-full bg-slate-50 rounded-xl p-3 border border-slate-200 group-hover:border-blue-200 transition-colors">
                        <p class="text-sm font-bold text-slate-700">SMK Muhammadiyah</p>
                        <p class="text-xs font-semibold text-slate-500 mt-1"><i class="fa-solid fa-location-dot text-blue-500 mr-1"></i> Tasikmalaya</p>
                    </div>
                </div>

                <!-- BENTO 3: Jurusan & Kelas (Span 5) -->
                <div class="md:col-span-5 bg-white border border-slate-200 rounded-[2rem] p-8 bento-box flex flex-col justify-between shadow-sm">
                    <div>
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 border-b border-slate-100 pb-2">KONSENTRASI STUDI</h4>
                        
                        <div class="flex items-start gap-4 mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0 shadow-sm">
                                <i class="fa-solid fa-laptop-code text-2xl"></i>
                            </div>
                            <div class="pt-1">
                                <h3 class="text-lg font-black text-slate-800 leading-tight">Rekayasa Perangkat Lunak</h3>
                                <p class="text-sm font-medium text-slate-500 mt-1">Pengembangan Web & Aplikasi</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0 shadow-sm">
                                <i class="fa-solid fa-chalkboard-user text-2xl"></i>
                            </div>
                            <div class="pt-1">
                                <h3 class="text-lg font-black text-slate-800 leading-tight">Kelas 12 XII</h3>
                                <p class="text-sm font-medium text-slate-500 mt-1">Tingkat Akhir SMK</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stack Pills -->
                    <div class="flex flex-wrap gap-2 mt-8">
                        <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-600">UI/UX Design</span>
                        <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-600">Frontend Web</span>
                        <span class="px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-600">Database</span>
                    </div>
                </div>

                <!-- BENTO 4: Editor JSON (Aksen Pembeda Gelap - Span 7) -->
                <!-- Menggunakan Dark Mode murni di dalam kotak ini agar menjadi aksen visual yang sangat mencolok ala programmer -->
                <div class="md:col-span-7 bg-[#0f172a] border border-slate-800 rounded-[2rem] p-0 shadow-xl overflow-hidden flex flex-col transform transition-transform duration-500 hover:-translate-y-1">
                    <!-- Mac Window Header -->
                    <div class="bg-[#1e293b] px-6 py-3 border-b border-slate-700/50 flex items-center justify-between">
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        <span class="text-xs font-mono font-medium text-slate-400 flex items-center gap-2">
                            <i class="fa-brands fa-js text-yellow-400"></i> rangga_profile.json
                        </span>
                        <div class="w-10"></div> <!-- Spacer untuk menengahkan judul -->
                    </div>
                    <!-- Code Area -->
                    <div class="p-6 md:p-8 font-mono text-sm md:text-[15px] leading-loose overflow-x-auto text-slate-300">
                        <span class="json-bracket">{</span><br>
                        &nbsp;&nbsp;<span class="json-key">"namaLengkap"</span>: <span class="json-string">"Rangga Ramadiyanto Nurhakim"</span>,<br>
                        &nbsp;&nbsp;<span class="json-key">"sekolah"</span>: <span class="json-string">"SMK Muhammadiyah"</span>,<br>
                        &nbsp;&nbsp;<span class="json-key">"jurusan"</span>: <span class="json-string">"Rekayasa Perangkat Lunak"</span>,<br>
                        &nbsp;&nbsp;<span class="json-key">"kelas"</span>: <span class="json-string">"XII RPL 1"</span>,<br>
                        &nbsp;&nbsp;<span class="json-key">"hobi"</span>: <span class="json-bracket">[</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="json-string">"Ngoding"</span>, <span class="json-string">"Desain Web"</span>, <span class="json-string">"E-Sports"</span><br>
                        &nbsp;&nbsp;<span class="json-bracket">]</span>,<br>
                        &nbsp;&nbsp;<span class="json-key">"status"</span>: <span class="json-number">200</span> <span class="text-slate-500 italic">/* Siap Belajar Hal Baru */</span><br>
                        <span class="json-bracket">}</span>
                        <!-- Kursor Blinking -->
                        <span class="inline-block w-2 h-4 bg-blue-400 ml-1 animate-pulse align-middle"></span>
                    </div>
                </div>

                <!-- BENTO 5: Social / Kontak (Span 12) - Aksen Warna Biru Gradient -->
                <div class="md:col-span-12 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-[2rem] p-6 md:p-8 shadow-lg shadow-blue-500/20 flex flex-col md:flex-row items-center justify-between gap-6 transform transition duration-300 hover:scale-[1.01]">
                    <div>
                        <h3 class="text-2xl font-black text-white mb-2">Mari Berkolaborasi!</h3>
                        <p class="text-sm font-medium text-blue-100">Punya ide proyek, tugas, atau ingin diskusi seputar teknologi?</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="https://github.com/ranggazeedrn-rgb" class="w-12 h-12 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-blue-700 transition-all shadow-sm backdrop-blur-sm">
                            <i class="fa-brands fa-github text-xl"></i>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-full bg-white/10 border border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-blue-700 transition-all shadow-sm backdrop-blur-sm">
                            <i class="fa-brands fa-instagram text-xl"></i>
                        </a>
                        <a href="gmailto:ranggazeedrn@gmail.com" class="w-12 h-12 rounded-full bg-white/10 bder border-white/20 flex items-center justify-center text-white hover:bg-white hover:text-blue-700 transition-all shadow-sm backdrop-blur-sm">
                            <i class="fa-regular fa-envelope text-xl"></i>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </main>
</body>
</html>