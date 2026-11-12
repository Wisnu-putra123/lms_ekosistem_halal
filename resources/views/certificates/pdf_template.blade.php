<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sertifikat Kelulusan - {{ $studentName }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            color: #1e293b;
        }

        /* Background Template Gambar */
        .bg-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .bg-container img {
            width: 100%;
            height: 100%;
            object-fit: fill;
        }

        /* Area Konten Utama di Tengah Bingkai */
        .content {
            position: absolute;
            top: 175px; /* Aman di bawah emblem emas atas */
            left: 95px;
            right: 95px;
            text-align: center;
        }
        .main-title {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 3.5px;
            color: #475569;
            text-transform: uppercase;
            margin: 0 0 6px 0;
        }
        .certificate-number {
            font-family: 'Courier', monospace;
            font-size: 12px;
            font-weight: bold;
            color: #64748b;
            margin: 0 0 36px 0;
        }
        .label-text {
            font-size: 13px;
            color: #475569;
            margin: 0 0 10px 0;
        }
        .student-name {
            font-family: 'Times New Roman', Times, serif;
            font-size: 36px;
            font-weight: bold;
            color: #0f172a;
            display: inline-block;
            border-bottom: 1.5px solid #cbd5e1;
            padding: 0 42px 8px 42px;
            margin: 0 0 22px 0;
        }
        .course-title {
            font-size: 20px;
            font-weight: bold;
            color: #123F35;
            margin: 6px 0 0 0;
            line-height: 1.35;
        }

        /* Area Footer Bawah (Ruang di atasnya dibiarkan kosong untuk TTD & Nama Pejabat) */
        .footer-area {
            position: absolute;
            bottom: 62px; /* Posisi sejajar di bawah garis tanda tangan template */
            left: 110px;
            right: 110px;
            font-size: 11px;
        }
        .footer-left {
            float: left;
            text-align: left;
            line-height: 1.55;
        }
        .footer-right {
            float: right;
            text-align: right;
            line-height: 1.5;
        }
        .instansi {
            font-weight: bold;
            color: #123F35;
            font-size: 12px;
        }
        .issued-date {
            color: #64748b;
        }
        .verification-label {
            color: #64748b;
            font-size: 10px;
        }
        .verification-box {
            display: inline-block;
            font-family: 'Courier', monospace;
            font-weight: bold;
            color: #123F35;
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 11px;
            margin-top: 2px;
        }
        .verify-url {
            font-size: 9px;
            color: #94a3b8;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    @if(!empty($bgPath))
        <div class="bg-container">
            <img src="file://{{ $bgPath }}" alt="Template Background">
        </div>
    @endif

    <!-- Konten Utama -->
    <div class="content">
        <div class="main-title">SERTIFIKAT KELULUSAN</div>
        <div class="certificate-number">No: {{ $certificateNumber }}</div>
        
        <p class="label-text">Diberikan kepada:</p>
        <div class="student-name">{{ $studentName }}</div>
        
        <p class="label-text">Atas keberhasilannya menyelesaikan seluruh modul pembelajaran pada kursus:</p>
        <div class="course-title">{{ $courseTitle }}</div>
    </div>

    <!-- Footer Kiri & Kanan -->
    <div class="footer-area">
        <div class="footer-left">
            <div class="instansi">LPP-JPH Universitas Telkom</div>
            <div class="issued-date">Diterbitkan: {{ $issuedDate }}</div>
        </div>

        <div class="footer-right">
            <div class="verification-label">Kode Verifikasi:</div>
            <div class="verification-box">{{ $verificationCode }}</div>
            <div class="verify-url">Verifikasi: {{ url('/verify-certificate/' . $verificationCode) }}</div>
        </div>
    </div>
</body>
</html>