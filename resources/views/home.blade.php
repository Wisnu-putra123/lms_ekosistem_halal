<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Platform pembelajaran Lembaga Pelaksana Pelatihan Jaminan Produk Halal.">
    <title>LPP-JPH | Pelatihan Jaminan Produk Halal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-50 text-slate-800 antialiased">
    @php
        $institutionName = 'LPP-JPH';
        $institutionFullName = 'Lembaga Pelaksana Pelatihan Jaminan Produk Halal';
        $institutionEmail = 'info@lpp-jph.example';
        $institutionPhone = '+62 812-0000-0000';
        $institutionAddress = 'Alamat lembaga, Kota, Provinsi';

        $aboutParagraphs = [
            'LPP-JPH merupakan lembaga pelatihan yang berfokus pada pengembangan pengetahuan dan kompetensi di bidang Jaminan Produk Halal.',
            'Melalui platform pembelajaran digital ini, peserta dapat mengakses materi, mengikuti evaluasi, mengumpulkan tugas, dan memantau perkembangan pembelajaran dalam satu tempat.',
        ];

        $learningFeatures = [
            ['icon' => 'book-open', 'title' => 'Materi Terstruktur', 'description' => 'Materi disusun berdasarkan course dan pertemuan agar peserta dapat belajar secara bertahap.'],
            ['icon' => 'clipboard-check', 'title' => 'Kuis dan Evaluasi', 'description' => 'Latih pemahaman melalui kuis, batas waktu jika diterapkan, dan hasil percobaan.'],
            ['icon' => 'file-text', 'title' => 'Tugas dan Umpan Balik', 'description' => 'Kumpulkan tugas sesuai metode yang ditentukan dan lihat feedback dari pengajar.'],
            ['icon' => 'chart-no-axes-column', 'title' => 'Pantau Progres', 'description' => 'Lihat perkembangan pembelajaran dan nilai dari aktivitas yang telah diselesaikan.'],
            ['icon' => 'award', 'title' => 'Informasi Sertifikat', 'description' => 'Pantau pemenuhan persyaratan dan status sertifikat sesuai kebijakan program.'],
            ['icon' => 'monitor-smartphone', 'title' => 'Belajar Secara Online', 'description' => 'Akses pembelajaran melalui browser pada perangkat yang terhubung ke internet.'],
        ];

        $trainingSteps = [
            ['number' => '01', 'title' => 'Buat Akun', 'description' => 'Daftarkan akun peserta dan lengkapi informasi yang diperlukan.'],
            ['number' => '02', 'title' => 'Pilih Program', 'description' => 'Pelajari informasi course yang tersedia dan ikuti program sesuai ketentuannya.'],
            ['number' => '03', 'title' => 'Ikuti Pembelajaran', 'description' => 'Pelajari materi, kerjakan kuis, dan kumpulkan tugas yang diwajibkan.'],
            ['number' => '04', 'title' => 'Selesaikan Persyaratan', 'description' => 'Penuhi ketentuan pembelajaran dan penilaian untuk proses penyelesaian serta sertifikat.'],
        ];

        $faqs = [
            ['question' => 'Bagaimana cara mengikuti pelatihan?', 'answer' => 'Buat akun melalui halaman Daftar, masuk ke platform, lalu ikuti petunjuk enrollment atau pendaftaran pada program yang tersedia. Detail prosedur dapat disesuaikan dengan kebijakan lembaga.'],
            ['question' => 'Apakah materi dapat diakses secara online?', 'answer' => 'Ya, platform dirancang untuk diakses melalui browser dengan koneksi internet. Ketersediaan setiap materi mengikuti pengaturan course.'],
            ['question' => 'Bagaimana cara mengetahui progres dan nilai?', 'answer' => 'Peserta dapat melihat progres serta hasil aktivitas pembelajaran melalui dashboard atau halaman nilai yang tersedia pada akun masing-masing.'],
            ['question' => 'Kapan sertifikat diberikan?', 'answer' => 'Sertifikat diproses setelah peserta memenuhi persyaratan program dan melalui proses pemeriksaan atau persetujuan sesuai kebijakan lembaga.'],
            ['question' => 'Ke mana saya harus menghubungi jika mengalami kendala?', 'answer' => 'Hubungi tim pengelola melalui informasi kontak yang tercantum di bagian Kontak. Ganti informasi contoh ini dengan kontak resmi lembaga sebelum website dipublikasikan.'],
        ];
    @endphp

    <!-- Navbar -->
    <header class="sticky top-0 z-50 border-b border-emerald-950/10 bg-white/95 shadow-sm backdrop-blur">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="#beranda" class="flex items-center gap-3" aria-label="LPP-JPH, Beranda">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-900 text-white">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3 19 6v5c0 4.5-3 7.8-7 10-4-2.2-7-5.5-7-10V6l7-3Z"/><path d="m8.5 12 2.2 2.2 4.8-5"/></svg>
                </span>
                <span class="leading-tight">
                    <span class="block text-lg font-extrabold tracking-tight text-emerald-950">{{ $institutionName }}</span>
                    <span class="hidden text-xs text-slate-500 sm:block">Pelatihan Jaminan Produk Halal</span>
                </span>
            </a>

            <div class="hidden items-center gap-6 lg:flex">
                <a href="#beranda" class="text-sm font-medium text-slate-600 hover:text-emerald-800">Beranda</a>
                <a href="#tentang" class="text-sm font-medium text-slate-600 hover:text-emerald-800">Tentang</a>
                <a href="#program" class="text-sm font-medium text-slate-600 hover:text-emerald-800">Program</a>
                <a href="#fitur" class="text-sm font-medium text-slate-600 hover:text-emerald-800">Fitur LMS</a>
                <a href="#alur" class="text-sm font-medium text-slate-600 hover:text-emerald-800">Alur Pelatihan</a>
                <a href="#faq" class="text-sm font-medium text-slate-600 hover:text-emerald-800">FAQ</a>
                <a href="#kontak" class="text-sm font-medium text-slate-600 hover:text-emerald-800">Kontak</a>
            </div>

            <div class="flex items-center gap-2 sm:gap-3">
                @auth
                    <a href="{{ route('home') }}" class="rounded-lg bg-emerald-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-3 py-2.5 text-sm font-semibold text-emerald-900 transition hover:bg-emerald-50">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-lg bg-emerald-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-800">Daftar</a>
                    @endif
                @endauth
            </div>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section id="beranda" class="relative isolate overflow-hidden bg-[#123F35] min-h-screen flex flex-col justify-center">
            <!-- Background Radial Gradient & Decorative Glow Rings -->
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-[#28745B]/60 via-[#123F35] to-[#123F35]"></div>
            <div class="absolute -left-20 -top-20 -z-10 h-96 w-96 rounded-full border border-white/10 blur-2xl"></div>
            <div class="absolute -right-20 -top-20 -z-10 h-96 w-96 rounded-full border border-white/10 blur-2xl"></div>

            <div class="mx-auto max-w-5xl px-4 py-20 text-center sm:px-6 sm:py-24 lg:px-8 lg:py-28 w-full flex-1 flex flex-col justify-center">

                <!-- Headline Utama -->
                <h1 class="mt-4 text-4xl font-extrabold leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                    LPP-JPH <br class="hidden sm:inline" />
                    <span class="text-[#E7C982]">Lembaga Pelaksana Pelatihan Jaminan Produk Halal</span>
                </h1>

                <!-- Deskripsi Platform -->
                <p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-emerald-100/85 sm:text-lg">
                    Platform pembelajaran digital {{ $institutionFullName }} untuk mendukung proses pelatihan melalui materi terstruktur, evaluasi pembelajaran, tugas, dan pemantauan progres peserta.
                </p>

                <!-- CTA Action Buttons -->
                <div class="mt-9 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <a href="#program" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-[#E7C982] px-7 py-3.5 font-bold text-[#123F35] shadow-lg shadow-[#E7C982]/10 transition hover:bg-[#e0be71] sm:w-auto">
                        Jelajahi Program
                    </a>
                    @auth
                        <a href="{{ route('home') }}" class="w-full inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/5 px-7 py-3.5 font-semibold text-white backdrop-blur-sm transition hover:bg-white/15 sm:w-auto">
                            Masuk ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center rounded-xl border border-white/20 bg-white/5 px-7 py-3.5 font-semibold text-white backdrop-blur-sm transition hover:bg-white/15 sm:w-auto">
                            Masuk LMS
                        </a>
                    @endauth
                </div>

                <!-- Text Pendukung -->
                <p class="mt-4 text-xs sm:text-sm text-emerald-100/70">Belajar terarah, evaluasi terukur, progres terdokumentasi.</p>

                <!-- Stat Cards Ringkas (Penyedia Visual & Pengisi Ruang) -->
                <div class="mt-14 grid grid-cols-1 gap-4 border-t border-white/10 pt-10 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center backdrop-blur-md transition hover:border-[#E7C982]/30">
                        <div class="text-xl sm:text-2xl font-bold text-[#E7C982]">Terstruktur</div>
                        <div class="mt-1 text-xs text-emerald-100/70">Materi & Pertemuan Sesuai Kurikulum</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center backdrop-blur-md transition hover:border-[#E7C982]/30">
                        <div class="text-xl sm:text-2xl font-bold text-[#E7C982]">Evaluatif</div>
                        <div class="mt-1 text-xs text-emerald-100/70">Kuis & Tugas Otomatis/Terpenilai</div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center backdrop-blur-md transition hover:border-[#E7C982]/30">
                        <div class="text-xl sm:text-2xl font-bold text-[#E7C982]">Sertifikasi</div>
                        <div class="mt-1 text-xs text-emerald-100/70">Penerbitan Sertifikat Digital Resmi</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About LPP-JPH -->
        <section id="tentang" class="scroll-mt-24 bg-[#F7F5EE] py-20 sm:py-24 relative overflow-hidden min-h-screen flex flex-col justify-center">
            <!-- Dekorasi Radial Latar Belakang Halus -->
            <div class="absolute -left-20 -top-20 -z-0 h-96 w-96 rounded-full bg-[#28745B]/10 blur-3xl"></div>
            <div class="absolute -right-20 -bottom-20 -z-0 h-96 w-96 rounded-full bg-[#E7C982]/20 blur-3xl"></div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-12 lg:items-center">
                    
                    <!-- Kolom Kiri: Judul & Pengenalan -->
                    <div class="lg:col-span-5 space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-[#28745B]/20 bg-[#28745B]/10 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-[#123F35]">
                            <span class="h-2 w-2 rounded-full bg-[#28745B]"></span>
                            Tentang Lembaga
                        </div>

                        <h2 class="text-3xl font-extrabold tracking-tight text-[#123F35] sm:text-4xl lg:text-5xl leading-tight">
                            Mendukung Pembelajaran <span class="text-[#28745B]">Jaminan Produk Halal</span>
                        </h2>

                        <p class="text-sm sm:text-base leading-relaxed text-[#123F35]/80 pt-2">
                            LPP-JPH hadir sebagai ekosistem pelatihan terpadu yang memfasilitasi pengembangan kompetensi SDM secara terstruktur, akuntabel, dan berbasis teknologi digital.
                        </p>
                    </div>

                    <!-- Kolom Kanan: Detail Paragraf & Kartu Komitmen -->
                    <div class="lg:col-span-7 space-y-6">
                        <!-- Grid Paragraf Utama -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($aboutParagraphs as $index => $paragraph)
                                <div class="p-5 rounded-2xl bg-white border border-[#123F35]/10 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
                                    <div class="w-8 h-8 rounded-lg bg-[#F7F5EE] text-[#123F35] font-bold text-xs flex items-center justify-center mb-3 border border-[#28745B]/20">
                                        0{{ $index + 1 }}
                                    </div>
                                    <p class="text-sm leading-6 text-slate-700 font-medium">{{ $paragraph }}</p>
                                </div>
                            @endforeach
                        </div>

                        <!-- Kartu Komitmen Pembelajaran (Highlight Warm Ivory & Muted Gold) -->
                        <div class="relative overflow-hidden rounded-2xl bg-[#123F35] p-6 text-white shadow-xl border border-[#E7C982]/30">
                            <!-- Accent Line Warna Gold -->
                            <div class="absolute top-0 left-0 h-1 w-full bg-[#E7C982]"></div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-[#E7C982] text-[#123F35] flex items-center justify-center font-bold flex-shrink-0 mt-0.5 shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <div class="space-y-1">
                                    <h3 class="font-bold text-base text-[#E7C982] tracking-wide">Komitmen Pembelajaran & Mutu</h3>
                                    <p class="text-xs sm:text-sm leading-relaxed text-emerald-100/90">
                                        Informasi profil, visi-misi, legalitas, serta ruang lingkup pelatihan senantiasa diperbarui dan diselaraskan secara berkala mengikuti dokumen resmi standar lembaga.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>

        <!-- Course Catalog / Program Pelatihan -->
        <section id="program" class="scroll-mt-24 bg-white py-16 sm:py-24 min-h-screen flex flex-col justify-center relative overflow-hidden">
            <!-- Dekorasi Radial Background Halus -->
            <div class="absolute -right-24 -top-24 -z-0 h-96 w-96 rounded-full bg-[#28745B]/10 blur-3xl"></div>
            <div class="absolute -left-24 -bottom-24 -z-0 h-96 w-96 rounded-full bg-[#E7C982]/15 blur-3xl"></div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full flex-1 flex flex-col justify-between">
                
                <!-- Section Header -->
                <div>
                    <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end border-b border-[#123F35]/10 pb-6">
                        <div class="max-w-2xl space-y-2">
                            <div class="inline-flex items-center gap-2 rounded-full border border-[#28745B]/20 bg-[#28745B]/10 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-[#123F35]">
                                <span class="h-2 w-2 rounded-full bg-[#28745B]"></span>
                                Program Pelatihan
                            </div>
                            <h2 class="text-3xl font-extrabold tracking-tight text-[#123F35] sm:text-4xl lg:text-5xl">
                                Temukan Program yang Sesuai
                            </h2>
                            <p class="text-sm sm:text-base leading-relaxed text-[#123F35]/75 pt-1">
                                Lihat ringkasan program pelatihan yang dipublikasikan oleh pengelola. Informasi ditampilkan berdasarkan data course pada platform.
                            </p>
                        </div>
                        
                        @auth
                            <a href="{{ route('home') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-[#123F35] px-5 py-3 text-sm font-bold text-white shadow-md transition hover:bg-[#28745B]">
                                Ke Dashboard Saya <span aria-hidden="true">→</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-[#123F35] px-5 py-3 text-sm font-bold text-white shadow-md transition hover:bg-[#28745B]">
                                Masuk untuk Belajar <span aria-hidden="true">→</span>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Grid Cards Course -->
                <div class="my-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 flex-1 items-start">
                    @isset($courses)
                        @forelse ($courses as $course)
                            <article class="group flex h-full flex-col overflow-hidden rounded-2xl border border-[#28745B]/15 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl hover:border-[#28745B]/30">
                                <!-- Card Header Green Palette -->
                                <div class="relative flex min-h-36 items-start justify-between gap-4 bg-[#28745B] p-6 text-white transition duration-300 group-hover:bg-[#123F35]">
                                    <div class="space-y-1">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#E7C982]">Program Pelatihan</span>
                                        <h3 class="text-lg font-bold leading-snug text-white line-clamp-2">{{ $course->title }}</h3>
                                    </div>
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 backdrop-blur-xs text-[#E7C982]">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </span>
                                </div>

                                <!-- Card Body -->
                                <div class="flex flex-1 flex-col justify-between p-6">
                                    <p class="flex-1 text-xs sm:text-sm leading-relaxed text-slate-600 line-clamp-4">
                                        {{ \Illuminate\Support\Str::limit($course->description ?: 'Informasi program pelatihan akan tersedia melalui halaman course.', 140) }}
                                    </p>
                                    
                                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                                        @if (!empty($course->code))
                                            <span class="inline-flex items-center rounded-md bg-[#F7F5EE] px-2.5 py-1 text-[11px] font-semibold text-[#123F35] border border-[#28745B]/15">
                                                {{ $course->code }}
                                            </span>
                                        @else
                                            <span></span>
                                        @endif

                                        @auth
                                            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#28745B] hover:text-[#123F35] transition">
                                                Lihat Dashboard <span aria-hidden="true">→</span>
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#28745B] hover:text-[#123F35] transition">
                                                Masuk Ikuti <span aria-hidden="true">→</span>
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="col-span-full rounded-2xl border border-dashed border-[#28745B]/30 bg-white/80 p-12 text-center backdrop-blur-xs">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#28745B]/10 text-[#28745B]">
                                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z"/>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-base font-bold text-[#123F35]">Program Belum Tersedia</h3>
                                <p class="mt-1 text-xs sm:text-sm text-slate-500">Belum ada course yang dipublikasikan. Silakan periksa kembali secara berkala.</p>
                            </div>
                        @endforelse
                    @else
                        <div class="col-span-full rounded-2xl border border-[#E7C982] bg-[#E7C982]/15 p-6 backdrop-blur-xs">
                            <p class="font-bold text-[#123F35]">Daftar course belum terhubung ke controller.</p>
                            <p class="mt-1 text-xs sm:text-sm text-[#123F35]/80">
                                Kirim variabel <code>$courses</code> ke view ini dan filter course dengan status <code>published</code>. Setelah terhubung, kartu program akan dibuat otomatis dari tabel <code>courses</code>.
                            </p>
                        </div>
                    @endisset
                </div>

                <!-- Section Footer Note -->
                <p class="text-center text-[11px] leading-relaxed text-slate-400">
                    Catatan: Durasi, jadwal, biaya, persyaratan peserta, dan kuota dapat disesuaikan pada konfigurasi modul kelas masing-masing.
                </p>

            </div>
        </section>

        <!-- Learning Features / Fitur LMS -->
        <section id="fitur" class="scroll-mt-24 bg-[#F7F5EE] py-16 sm:py-24 min-h-screen flex flex-col justify-center relative overflow-hidden">
            <!-- Dekorasi Radial Background Halus -->
            <div class="absolute -left-24 -top-24 -z-0 h-96 w-96 rounded-full bg-[#28745B]/10 blur-3xl"></div>
            <div class="absolute -right-24 -bottom-24 -z-0 h-96 w-96 rounded-full bg-[#E7C982]/20 blur-3xl"></div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full flex-1 flex flex-col justify-center">
                
                <!-- Header Section -->
                <div class="mx-auto max-w-2xl text-center space-y-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#28745B]/20 bg-[#28745B]/10 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-[#123F35]">
                        <span class="h-2 w-2 rounded-full bg-[#28745B]"></span>
                        Pembelajaran Digital
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-[#123F35] sm:text-4xl lg:text-5xl">
                        Fitur untuk Mendukung Proses Belajar
                    </h2>
                    <p class="text-sm sm:text-base leading-relaxed text-[#123F35]/75">
                        Seluruh aktivitas pembelajaran dirancang agar lebih terstruktur, terintegrasi, dan mudah dipantau secara real-time.
                    </p>
                </div>

                <!-- Cards Grid -->
                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($learningFeatures as $feature)
                        <article class="group relative rounded-2xl border border-[#28745B]/15 bg-white p-7 shadow-sm transition duration-300 hover:-translate-y-1 hover:border-[#28745B]/40 hover:shadow-xl flex flex-col justify-between">
                            <div>
                                <!-- Icon Box Warm Ivory + Green Border -->
                                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#F7F5EE] text-[#28745B] border border-[#28745B]/20 transition duration-300 group-hover:bg-[#28745B] group-hover:text-white group-hover:border-[#28745B]">
                                    @if ($feature['icon'] === 'book-open')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 6.5C9.5 4.5 6.5 4 3 5v14c3.5-1 6.5-.5 9 1.5m0-14C14.5 4.5 17.5 4 21 5v14c-3.5-1-6.5-.5-9 1.5m0-14v14"/></svg>
                                    @elseif ($feature['icon'] === 'clipboard-check')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4.5h6V7H9zM8 13l2.5 2.5L16 10"/></svg>
                                    @elseif ($feature['icon'] === 'file-text')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 3h8l4 4v14H6z"/><path d="M14 3v5h5M9 12h6M9 16h6"/></svg>
                                    @elseif ($feature['icon'] === 'chart-no-axes-column')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 20V10h4v10M10 20V4h4v16M16 20v-7h4v7M2 20h20"/></svg>
                                    @elseif ($feature['icon'] === 'award')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="5"/><path d="m8.5 12-1 9 4.5-2.5 4.5 2.5-1-9"/></svg>
                                    @else
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                                    @endif
                                </div>

                                <!-- Content -->
                                <h3 class="mt-5 text-lg font-bold text-[#123F35] transition duration-200 group-hover:text-[#28745B]">
                                    {{ $feature['title'] }}
                                </h3>
                                <p class="mt-2 text-xs sm:text-sm leading-relaxed text-slate-600">
                                    {{ $feature['description'] }}
                                </p>
                            </div>

                            <!-- Bottom Gold Accent Line (Hover Effect) -->
                            <div class="mt-6 h-1 w-0 bg-[#E7C982] transition-all duration-300 group-hover:w-full rounded-full"></div>
                        </article>
                    @endforeach
                </div>

            </div>
        </section>

        <!-- Training Flow / Alur Pelatihan -->
        <section id="alur" class="scroll-mt-24 bg-[#123F35] py-16 sm:py-24 text-white min-h-screen flex flex-col justify-center relative overflow-hidden">
            <!-- Dekorasi Background Radial & Ring -->
            <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_bottom_right,_var(--tw-gradient-stops))] from-[#28745B]/40 via-[#123F35] to-[#123F35]"></div>
            <div class="absolute -left-20 -bottom-20 -z-0 h-96 w-96 rounded-full border border-[#E7C982]/10 blur-xl"></div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full flex-1 flex flex-col justify-center">
                
                <!-- Header Section -->
                <div class="mx-auto max-w-2xl text-center space-y-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-[#E7C982]/30 bg-[#E7C982]/10 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-[#E7C982] backdrop-blur-sm">
                        <span class="h-2 w-2 rounded-full bg-[#E7C982] animate-pulse"></span>
                        Alur Pelatihan
                    </div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                        Empat Langkah Mengikuti Pembelajaran
                    </h2>
                    <p class="text-sm sm:text-base leading-relaxed text-emerald-100/80">
                        Gambaran umum proses peserta dari pendaftaran sampai menyelesaikan seluruh persyaratan program.
                    </p>
                </div>

                <!-- Steps Grid dengan Line Connector -->
                <div class="relative mt-14">
                    <!-- Garis Penghubung Alur (Hanya Tampil di Layar Desktop Large) -->
                    <div class="hidden lg:block absolute top-12 left-[12%] right-[12%] h-0.5 bg-gradient-to-r from-[#28745B] via-[#E7C982]/60 to-[#28745B] -z-0"></div>

                    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 relative z-10">
                        @foreach ($trainingSteps as $index => $step)
                            <article class="group relative rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur-md transition duration-300 hover:-translate-y-1 hover:border-[#E7C982]/50 hover:bg-white/10 hover:shadow-2xl flex flex-col justify-between">
                                <div>
                                    <!-- Header Kartu: Nomor Langkah & Indicator -->
                                    <div class="flex items-center justify-between mb-5">
                                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#E7C982] text-base font-black text-[#123F35] shadow-lg shadow-[#E7C982]/20 transition duration-300 group-hover:scale-110">
                                            {{ $step['number'] }}
                                        </span>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-200/50">
                                            Langkah 0{{ $index + 1 }}
                                        </span>
                                    </div>

                                    <!-- Judul & Deskripsi -->
                                    <h3 class="text-lg font-bold text-white transition duration-200 group-hover:text-[#E7C982]">
                                        {{ $step['title'] }}
                                    </h3>
                                    <p class="mt-2.5 text-xs sm:text-sm leading-relaxed text-emerald-100/75">
                                        {{ $step['description'] }}
                                    </p>
                                </div>

                                <!-- Indicator Dot Aksesibilitas di Bawah -->
                                <div class="mt-6 flex items-center gap-1.5 pt-4 border-t border-white/10">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#E7C982]"></span>
                                    <span class="text-[11px] font-medium text-emerald-200/60">Proses Berurutan</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                <!-- Footer Callout Note -->
                <div class="mt-12 text-center">
                    <p class="inline-flex items-center gap-2 rounded-xl bg-[#28745B]/30 border border-[#28745B]/50 px-4 py-2 text-xs text-emerald-100/90">
                        <svg class="w-4 h-4 text-[#E7C982] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Selesaikan setiap tahapan secara berurutan untuk mendapatkan evaluasi dan sertifikat resmi.</span>
                    </p>
                </div>

            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="scroll-mt-24 bg-[#F7F5EE] py-16 sm:py-24 min-h-screen flex flex-col justify-center relative overflow-hidden">
            <!-- Dekorasi Radial Background Halus -->
            <div class="absolute -right-20 -top-20 -z-0 h-96 w-96 rounded-full bg-[#28745B]/10 blur-3xl"></div>
            <div class="absolute -left-20 -bottom-20 -z-0 h-96 w-96 rounded-full bg-[#E7C982]/15 blur-3xl"></div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full flex-1 flex flex-col justify-center">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-start">
                    
                    <!-- Kolom Kiri: Header & Callout -->
                    <div class="lg:col-span-5 space-y-4">
                        <div class="inline-flex items-center gap-2 rounded-full border border-[#28745B]/20 bg-[#28745B]/10 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-[#123F35]">
                            <span class="h-2 w-2 rounded-full bg-[#28745B]"></span>
                            FAQ
                        </div>

                        <h2 class="text-3xl font-extrabold tracking-tight text-[#123F35] sm:text-4xl lg:text-5xl leading-tight">
                            Pertanyaan yang Sering Diajukan
                        </h2>

                        <p class="text-sm sm:text-base leading-relaxed text-[#123F35]/75 pt-1">
                            Informasi singkat untuk membantu calon peserta memahami proses pendaftaran, pembelajaran, evaluasi, hingga sertifikasi.
                        </p>

                        <!-- Box Bantuan Kontak -->
                        <div class="pt-4">
                            <div class="rounded-2xl border border-[#28745B]/20 bg-white p-5 shadow-sm space-y-2">
                                <div class="flex items-center gap-2 text-[#123F35] font-bold text-sm">
                                    <svg class="w-5 h-5 text-[#28745B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Mengalami Kendala Lain?</span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Jika kendala Anda belum terjawab, silakan kirimkan pertanyaan atau hubungi tim pengelola melalui kontak resmi lembaga.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Loop List FAQ Accordion -->
                    <div class="lg:col-span-7 space-y-3.5">
                        @foreach ($faqs as $faq)
                            <details class="group rounded-2xl border border-[#28745B]/15 bg-white p-5 transition-all duration-300 shadow-sm hover:border-[#28745B]/40 open:border-[#28745B] open:shadow-md">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 font-bold text-[#123F35] transition duration-200 group-hover:text-[#28745B]">
                                    <span class="text-sm sm:text-base leading-snug">{{ $faq['question'] }}</span>
                                    <!-- Icon Indicator (+) yang Berubah Jadi (x) Warna Emas Saat Open -->
                                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#F7F5EE] text-[#123F35] font-bold text-lg border border-[#28745B]/20 transition duration-300 group-open:rotate-45 group-open:bg-[#123F35] group-open:text-[#E7C982] group-open:border-[#123F35]" aria-hidden="true">
                                        +
                                    </span>
                                </summary>
                                <div class="mt-4 pt-3.5 border-t border-slate-100 text-xs sm:text-sm leading-relaxed text-slate-600">
                                    {{ $faq['answer'] }}
                                </div>
                            </details>
                        @endforeach
                    </div>

                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="kontak" class="scroll-mt-24 bg-white py-16 sm:py-24 min-h-screen flex flex-col justify-center relative overflow-hidden">
            <!-- Dekorasi Radial Background Halus -->
            <div class="absolute -left-20 -top-20 -z-0 h-96 w-96 rounded-full bg-[#28745B]/10 blur-3xl"></div>
            <div class="absolute -right-20 -bottom-20 -z-0 h-96 w-96 rounded-full bg-[#E7C982]/15 blur-3xl"></div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 w-full flex-1 flex flex-col justify-center">
                <!-- Main Card Banner (Deep Forest Green) -->
                <div class="relative overflow-hidden rounded-3xl bg-[#123F35] p-7 text-white shadow-2xl sm:p-10 lg:p-12 border border-[#E7C982]/20">
                    <!-- Decorative Inner Gradient & Accent Circle -->
                    <div class="absolute inset-0 -z-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-[#28745B]/50 via-[#123F35] to-[#123F35]"></div>
                    <div class="absolute -right-16 -bottom-16 -z-0 h-80 w-80 rounded-full border border-white/10 blur-md"></div>

                    <div class="relative z-10 grid gap-10 lg:grid-cols-12 lg:items-center">
                        
                        <!-- Left Side: Heading & Deskripsi -->
                        <div class="lg:col-span-6 space-y-4">
                            <div class="inline-flex items-center gap-2 rounded-full border border-[#E7C982]/30 bg-[#E7C982]/10 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-[#E7C982] backdrop-blur-sm">
                                <span class="h-2 w-2 rounded-full bg-[#E7C982]"></span>
                                Hubungi Kami
                            </div>
                            
                            <h2 class="text-2xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-4xl leading-tight">
                                Butuh Informasi Lebih Lanjut?
                            </h2>
                            
                            <p class="max-w-xl text-sm sm:text-base leading-relaxed text-emerald-100/80">
                                Hubungi pengelola <span class="font-semibold text-[#E7C982]">{{ $institutionName }}</span> untuk pertanyaan mengenai program pelatihan, prosedur pendaftaran, maupun kendala penggunaan platform pembelajaran.
                            </p>
                        </div>

                        <!-- Right Side: Contact Details Box -->
                        <div class="lg:col-span-6 space-y-5 rounded-2xl border border-white/15 bg-white/5 p-6 sm:p-7 backdrop-blur-md shadow-lg">
                            <!-- Email -->
                            <div class="flex items-start gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#E7C982] text-[#123F35] font-bold shadow-md">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-[#E7C982]">Email Resmi</p>
                                    <a href="mailto:{{ $institutionEmail }}" class="mt-0.5 inline-block font-semibold text-sm sm:text-base text-white hover:text-[#E7C982] transition truncate max-w-xs sm:max-w-md">
                                        {{ $institutionEmail }}
                                    </a>
                                </div>
                            </div>

                            <!-- Telepon -->
                            <div class="flex items-start gap-4 pt-3 border-t border-white/10">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#E7C982] text-[#123F35] font-bold shadow-md">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-[#E7C982]">Telepon / WhatsApp</p>
                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $institutionPhone) }}" class="mt-0.5 inline-block font-semibold text-sm sm:text-base text-white hover:text-[#E7C982] transition">
                                        {{ $institutionPhone }}
                                    </a>
                                </div>
                            </div>

                            <!-- Alamat -->
                            <div class="flex items-start gap-4 pt-3 border-t border-white/10">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#E7C982] text-[#123F35] font-bold shadow-md">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-[#E7C982]">Kantor / Alamat</p>
                                    <p class="mt-0.5 font-medium text-xs sm:text-sm text-emerald-100/90 leading-relaxed">
                                        {{ $institutionAddress }}
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-stone-200 bg-stone-50">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <div>
                <p class="font-extrabold text-emerald-950">{{ $institutionName }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ $institutionFullName }}</p>
            </div>
            <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-500">
                <a href="#tentang" class="hover:text-emerald-800">Tentang</a>
                <a href="#program" class="hover:text-emerald-800">Program</a>
                <a href="#faq" class="hover:text-emerald-800">FAQ</a>
                <a href="#kontak" class="hover:text-emerald-800">Kontak</a>
            </div>
            <p class="text-sm text-slate-500">&copy; {{ date('Y') }} {{ $institutionName }}. Hak cipta dilindungi.</p>
        </div>
    </footer>
</body>
</html>
