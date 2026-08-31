<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tanda Tangan Berita Acara Sidang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(191, 87, 0, 0.15);
            overflow: hidden;
            border-top: 6px solid #f7941d;
        }

        .header {
            background: linear-gradient(135deg, #ef6c00 0%, #f7941d 55%, #fbb040 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -10%;
            width: 260px;
            height: 260px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -60%;
            left: -5%;
            width: 220px;
            height: 220px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 50%;
        }

        .header h1 {
            font-size: 2.2em;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
            position: relative;
        }

        .header p {
            font-size: 1.1em;
            opacity: 0.95;
            position: relative;
        }

        .header .header-tag {
            display: inline-block;
            margin-top: 12px;
            padding: 4px 14px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 20px;
            font-size: 1.2em;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            position: relative;
        }

        .main-content {
            padding: 30px;
        }

        .document-container {
            background: #fffaf5;
            border: 1px solid #ffe0b2;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
        }

        .meta {
            background: #fff0e0;
            border: 1px solid #e8c9a8;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 26px;
        }

        .meta table {
            width: 100%;
            border-collapse: collapse;
            color: #4a3728;
        }

        .meta td {
            padding: 5px 8px;
            vertical-align: top;
        }

        .meta td:first-child {
            width: 180px;
            font-weight: 700;
            color: #8a5a2b;
        }

        .document-title {
            text-align: center;
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 6px;
            color: #b34700;
            letter-spacing: 0.3px;
        }

        .document-title-underline {
            width: min(100%, 640px);
            height: 4px;
            background: #f7941d;
            border-radius: 4px;
            margin: 0 auto 24px auto;
        }

        .document-number {
            text-align: center;
            color: #8a5a2b;
            font-weight: 600;
            margin-bottom: 22px;
        }

        .document-content {
            line-height: 1.8;
            color: #4a3728;
            margin-bottom: 30px;
        }

        .document-content strong {
            color: #b34700;
        }

        .document-content table.majelis-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
            background: #fff6ec;
            border: 1px solid #f7941d;
            border-radius: 6px;
            overflow: hidden;
        }

        .document-content table.majelis-table th,
        .document-content table.majelis-table td {
            border: 1px solid #e8c9a8;
            padding: 10px 14px;
            text-align: left;
        }

        .document-content table.majelis-table th {
            background: #fff0e0;
            color: #8a5a2b;
        }

        .document-closing {
            margin-top: 16px;
        }

        .back-button {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #ef6c00;
            color: white;
            text-decoration: none;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 0.85em;
            font-weight: 600;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(239, 108, 0, 0.35);
        }

        .back-button:hover {
            color: white;
            background: #d35400;
            transform: translateY(-1px);
        }

        .share-button {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ef6c00;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 0.85em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(239, 108, 0, 0.35);
        }

        .share-button:hover {
            background: #d35400;
            transform: translateY(-1px);
        }

        .share-button:active {
            transform: translateY(0);
        }

        .ettd-tabs {
            display: flex;
            gap: 8px;
            border-bottom: 1px solid #e8c9a8;
            margin-bottom: 24px;
        }

        .ettd-tab {
            border: 0;
            background: transparent;
            color: #8a5a2b;
            padding: 12px 20px;
            cursor: pointer;
            font: inherit;
        }

        .ettd-tab.active {
            color: #b34700;
            border-bottom: 3px solid #f7941d;
            font-weight: 600;
        }

        .ettd-panel {
            display: none;
        }

        .ettd-panel.active {
            display: block;
        }

        .ettd-notes-table {
            width: 100%;
            border-collapse: collapse;
            color: #4a3728;
        }

        .ettd-notes-table th,
        .ettd-notes-table td {
            border: 1px solid #e8c9a8;
            padding: 12px;
            text-align: left;
            vertical-align: top;
            white-space: pre-line;
        }

        .ettd-notes-table th {
            background: #fff0e0;
            color: #8a5a2b;
            font-weight: 600;
        }

        .ettd-notes-table tr:nth-child(even) td {
            background: #fffaf5;
        }

        .text-muted {
            color: #8a5a2b !important;
            opacity: 0.75;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .ettd-modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 3000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(74, 55, 40, 0.55);
        }

        .ettd-modal-backdrop.show {
            display: flex;
        }

        .ettd-modal-card {
            width: min(100%, 520px);
            background: #fffaf5;
            border: 1px solid #e8c9a8;
            border-radius: 12px;
            box-shadow: 0 12px 35px rgba(74, 55, 40, 0.25);
            overflow: hidden;
        }

        .ettd-modal-header,
        .ettd-modal-footer {
            padding: 16px 20px;
            background: #fff0e0;
        }

        .ettd-modal-header {
            border-bottom: 1px solid #e8c9a8;
        }

        .ettd-modal-footer {
            border-top: 1px solid #e8c9a8;
            text-align: right;
        }

        .ettd-modal-body {
            padding: 20px;
            color: #4a3728;
        }

        .ettd-modal-title {
            margin: 0;
            color: #8a5a2b;
            font-size: 1.15rem;
        }

        .ettd-modal-close {
            border: 0;
            background: transparent;
            color: #8a5a2b;
            font-size: 1.5rem;
            float: right;
            cursor: pointer;
        }

        .ettd-share-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e8c9a8;
            border-radius: 6px;
            color: #4a3728;
            background: #fff;
        }

        .ettd-modal-footer .btn+.btn {
            margin-left: 8px;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .signer {
            border: 1px solid #e8c9a8;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: white;
        }

        .signer h3 {
            margin: 0 0 6px;
            color: #b34700;
            font-size: 1.05rem;
        }

        .signer .signer-name {
            font-weight: bold;
            color: #2c2c2c;
            min-height: 20px;
        }

        .signer .hint {
            color: #8a5a2b;
            font-size: 0.85rem;
            opacity: 0.85;
        }

        .signature-box {
            height: 120px;
            border: 2px dashed #f7941d;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: white;
            margin: 14px 0;
            transition: all 0.2s ease;
            position: relative;
        }

        .signature-box:hover {
            border-color: #d35400;
            box-shadow: 0 3px 10px rgba(247, 148, 29, 0.25);
        }

        .signature-box.signed {
            border: 2px solid #ef6c00;
            border-style: solid;
            background: #fffaf3;
            cursor: default;
        }

        .signature-box img {
            max-width: 90%;
            max-height: 100px;
            object-fit: contain;
        }

        .signature-placeholder {
            color: #b98a55;
            font-size: 0.85em;
            text-align: center;
            width: 90%;
            pointer-events: none;
        }

        .controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .btn-ettd {
            background: #ef6c00;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            margin-bottom: 20px;
            font-size: 0.9em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(239, 108, 0, 0.35);
            text-decoration: none;
            display: inline-block;
        }

        .btn-ettd:hover {
            background: #d35400;
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-ettd-reset {
            background: #5b7d96;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 10px 22px;
            font-size: 0.9em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(91, 125, 150, 0.35);
        }

        .btn-ettd-reset:hover {
            background: #45647c;
            transform: translateY(-1px);
            color: #fff;
        }

        .btn-ettd:active,
        .btn-ettd-reset:active {
            transform: translateY(0);
        }

        .status-info {
            background: #fff6ec;
            border: 1px solid #ffd699;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            color: #4a3728;
            font-weight: 600;
        }

        .status-incomplete {
            background: #fff3cd;
            border: 1px solid #ffc107;
        }

        .status-complete {
            background: #ef6c00;
            border: 2px solid #d35400;
            color: white;
        }

        /* Modal tanda tangan */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            inset: 0;
            background: rgba(74, 40, 10, 0.55);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            width: min(100%, 600px);
            position: relative;
            border-top: 5px solid #f7941d;
        }

        .modal-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #b34700;
            margin-bottom: 8px;
        }

        .modal-header p {
            color: #8a5a2b;
        }

        .canvas-container {
            border: 2px solid #f7941d;
            border-radius: 10px;
            margin: 20px 0;
            background: white;
            overflow: hidden;
        }

        .canvas-container canvas {
            display: block;
            width: 100%;
            height: 220px;
            touch-action: none;
        }

        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .signature-method-tabs {
            display: flex;
            gap: 8px;
            border-bottom: 1px solid #e8c9a8;
            margin: 16px 0;
        }

        .signature-method-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 14px;
        }

        .signature-method-tab {
            border: 0;
            background: transparent;
            color: #8a5a2b;
            padding: 10px 16px;
            cursor: pointer;
            font: inherit;
        }

        .signature-method-tab.active {
            color: #b34700;
            border-bottom: 3px solid #f7941d;
            font-weight: 600;
        }

        .signature-method-panel {
            display: none;
        }

        .signature-method-panel.active {
            display: block;
        }

        .upload-help {
            color: #8a5a2b;
            background: #fff0e0;
            padding: 12px;
            border-radius: 6px;
        }

        .upload-error {
            color: #b42318;
            margin-top: 8px;
            min-height: 20px;
        }

        .upload-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 16px 0;
            color: #8a5a2b;
            font-weight: 600;
        }

        .upload-loading-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid #e8c9a8;
            border-top-color: #f7941d;
            border-radius: 50%;
            animation: ettd-spin 0.8s linear infinite;
        }

        @keyframes ettd-spin {
            to {
                transform: rotate(360deg);
            }
        }

        #signatureFile {
            display: block;
            width: 100%;
            padding: 6px 10px;
            font-size: 0.815rem;
            font-weight: 400;
            line-height: 1.5;
            color: #4a3728;
            background-color: #fff;
            border: 1px solid #d4c3a8;
            border-radius: 6px;
        }

        #signatureFile::-webkit-file-upload-button {
            padding: 5px 12px;
            margin: -6px 10px -6px -10px;
            background: #ef6c00;
            color: #fff;
            border: 0;
            border-radius: 4px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        #signatureFile::-webkit-file-upload-button:hover {
            background: #d35400;
        }

        #signatureFile::file-selector-button {
            padding: 5px 12px;
            margin-right: 10px;
            background: #ef6c00;
            color: #fff;
            border: 0;
            border-radius: 4px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        #signatureFile::file-selector-button:hover {
            background: #d35400;
        }

        #signatureFile:focus {
            border-color: #f7941d;
            box-shadow: 0 0 0 3px rgba(247, 148, 29, 0.12);
            outline: 0;
        }

        .form-label {
            display: inline-block;
            margin-bottom: 6px;
            font-size: 0.9em;
            font-weight: 500;
            color: #694a2c;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .mt-2 {
            margin-top: 0.5rem !important;
        }

        .mt-3 {
            margin-top: 1rem !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .form-control-sm {
            padding: 6px 10px;
            font-size: 0.815rem;
        }

        .signature-preview {
            max-width: 100%;
            max-height: 180px;
            margin: 16px auto;
            display: block;
            object-fit: contain;
        }

        .btn {
            padding: 12px 26px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 0.95em;
            font-weight: 600;
            transition: all 0.2s ease;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: #ef6c00;
            color: white;
        }

        .btn-primary:hover {
            background: #d35400;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #e0d5c7;
            color: #4a3728;
        }

        .btn-secondary:hover {
            background: #cdbfab;
        }

        .close {
            position: absolute;
            right: 15px;
            top: 15px;
            font-size: 26px;
            font-weight: bold;
            cursor: pointer;
            color: #b98a55;
        }

        .close:hover {
            color: #4a3728;
        }

        /* Celebration Effects */
        .celebration-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
        }

        .confetti {
            position: absolute;
            width: 9px;
            height: 9px;
            background: #f7941d;
            animation: fall 3s linear infinite;
        }

        .confetti:nth-child(2n) {
            background: #ef6c00;
        }

        .confetti:nth-child(3n) {
            background: #fbb040;
        }

        .confetti:nth-child(4n) {
            background: #ffd699;
        }

        .confetti:nth-child(5n) {
            background: #d35400;
        }

        @keyframes fall {
            0% {
                transform: translateY(-100vh) rotate(0deg);
                opacity: 1;
            }

            100% {
                transform: translateY(100vh) rotate(720deg);
                opacity: 0;
            }
        }

        .celebration-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #ef6c00, #f7941d, #fbb040);
            color: white;
            padding: 30px 50px;
            border-radius: 20px;
            font-size: 1.8em;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            z-index: 10000;
            opacity: 0;
            animation: celebration-popup 4s ease-in-out;
        }

        @keyframes celebration-popup {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.5);
            }

            20% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.05);
            }

            30% {
                transform: translate(-50%, -50%) scale(1);
            }

            80% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }

            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.8);
            }
        }

        .signature-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ef6c00 0%, #f7941d 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
            z-index: 10000;
            font-size: 14px;
            max-width: 300px;
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .signatures {
                grid-template-columns: 1fr;
            }

            .controls {
                flex-direction: column;
                align-items: center;
            }

            .meta td:first-child {
                width: 130px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-tag">SIDANG MAJELIS AKREDITASI</div>
            <h1>PENANDATANGANAN DIGITAL</h1>
            <p>{{ strtoupper($pengajuan->profile->nama_lembaga ?? 'LEMBAGA BELUM DITENTUKAN') }}</p>
        </div>

        <div class="main-content">
            <div class="ettd-tabs" role="tablist">
                <button class="ettd-tab active" type="button" role="tab" data-tab="signatureTab">Tanda
                    Tangan</button>
                <button class="ettd-tab" type="button" role="tab" data-tab="recommendationTab">Rekomendasi Hasil
                    <button class="ettd-tab" type="button" role="tab" data-tab="notesTab">Catatan Asesor</button>
                    Akreditasi</button>
            </div>
            @unless ($sidangAssessmentSubmitted)
                <div class="status-info status-incomplete" role="alert">
                    Penilaian Sidang Majelis belum disubmit oleh asesor. Penilaian tersebut wajib disubmit terlebih dahulu
                    sebelum melakukan tanda tangan Berita Acara Sidang.
                </div>
            @endunless
            <div class="ettd-panel active" id="signatureTab" role="tabpanel">
                <div class="document-container">
                    <a class="back-button" href="{{ $backUrl }}"><span aria-hidden="true">&#8592;</span> Kembali ke
                        Penilaian Final</a>
                    <button class="share-button" onclick="shareDocument()">Share Link</button>

                    <div class="document-title">
                        BERITA ACARA<br>
                        SIDANG MAJELIS AKREDITASI
                    </div>
                    <div class="document-number">Nomor {{ $nomorSurat ?? '-' }}</div>
                    <div class="document-title-underline"></div>

                    <div class="document-content">
                        <p>Pada hari ini <strong>{{ $hariTanggalSurat ?? 'Belum ditentukan' }}</strong>, pukul
                            <strong>{{ $waktuSurat ?? '-' }} {{ $zonaSurat ?? '-' }}</strong>, tempat
                            <strong>{{ $tempatSurat ?? '-' }}</strong> telah diselenggarakan Sidang Majelis
                            Akreditasi Program Pelatihan Teknis di Bidang
                            <strong>{{ $pengajuan->id_jenis == 1 ? 'Sistem Teknologi Berbasis Komputer' : 'Statistik' }}</strong>
                            untuk <strong>{{ $pengajuan->profile->nama_lembaga ?? 'Belum ditentukan' }}</strong> dengan
                            susunan tim Majelis Akreditasi sebagai berikut:
                        </p>

                        <table class="majelis-table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jabatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1. {{ $signatures->get('ketua_majelis')->nama_user ?? 'Belum ditentukan' }}
                                    </td>
                                    <td>Ketua</td>
                                </tr>
                                <tr>
                                    <td>2.
                                        {{ $signatures->get('sekretaris_majelis')->nama_user ?? 'Belum ditentukan' }}
                                    </td>
                                    <td>Sekretaris</td>
                                </tr>
                                <tr>
                                    <td>3. {{ $signatures->get('anggota_majelis')->nama_user ?? 'Belum ditentukan' }}
                                    </td>
                                    <td>Anggota</td>
                                </tr>
                            </tbody>
                        </table>

                        <p>Berdasarkan hasil penilaian akreditasi yang dilakukan oleh Majelis Akreditasi dihasilkan
                            penetapan status dan rekomendasi sebagai berikut (terlampir).</p>

                        <p class="document-closing"><strong>Demikian, Berita Acara ini dibuat untuk dapat dipergunakan
                                sebagaimana mestinya.</strong></p>
                    </div>

                    <div class="signatures">
                        @foreach (['ketua_majelis' => 'Ketua Majelis', 'sekretaris_majelis' => 'Sekretaris Majelis', 'anggota_majelis' => 'Anggota Majelis'] as $type => $label)
                            @php
                                $signature = $signatures->get($type);
                                $signed = $signature && $signature->status_ttd === 'signed' && $signature->ttd;
                            @endphp
                            <article class="signer">
                                <h3>{{ $label }}</h3>
                                <div class="signer-name">{{ $signature->nama_user ?? 'Belum diisi' }}</div>
                                <div class="signature-box {{ $signed ? 'signed' : '' }}"
                                    data-signer="{{ $type }}" data-signed="{{ $signed ? '1' : '0' }}"
                                    @if ($sidangAssessmentSubmitted) onclick="openSignature('{{ $type }}')" @else aria-disabled="true" @endif>
                                    @if ($signed)
                                        <img src="{{ asset($signature->ttd) }}"
                                            alt="Tanda tangan {{ $label }}">
                                    @else
                                        <div class="signature-placeholder">Klik untuk tanda tangan</div>
                                    @endif
                                </div>
                                <div class="hint">{{ $signed ? 'Sudah ditandatangani' : 'Belum ditandatangani' }}
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div id="status" class="status-info status-incomplete">
                        <span id="statusText">Memuat status tanda tangan...</span>
                    </div>

                    <input type="hidden" id="beritaAcaraStatus" value="{{ $submitted ? 'submitted' : '' }}">
                    <input type="hidden" id="canManage"
                        value="{{ auth()->check() && auth()->user()->role == 2 ? '1' : '0' }}">
                    <input type="hidden" id="sidangAssessmentStatus"
                        value="{{ $sidangAssessmentSubmitted ? 'submitted' : '' }}">

                    <div class="controls" id="submitControls" style="display: none; text-align: center;">
                        @if ($sidangAssessmentSubmitted && $isSekretariat)
                            @if (!$baSubmitted)
                                <button class="btn-ettd" id="submitBaBtn" onclick="submitDocument()">SUBMIT BERITA ACARA
                                    SIDANG</button>
                                <button class="btn-ettd-reset" id="resetAllSignaturesBtn" onclick="resetAllSignatures()"
                                    style="display:none;">RESET TANDA TANGAN</button>
                            @endif
                            <button class="btn-ettd-reset" id="resetBaBtn" onclick="resetBeritaAcara()"
                                style="display:{{ $baSubmitted ? 'inline-block' : 'none' }};">
                                RESET BERITA ACARA SIDANG
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="ettd-panel" id="notesTab" role="tabpanel">
                <div class="document-container">
                    <h3 style="color:#b34700; margin-bottom: 16px;">Catatan Sidang Majelis</h3>
                    @if ($catatanSidang->isNotEmpty())
                        <div class="table-responsive">
                            <table class="ettd-notes-table">
                                <thead>
                                    <tr>
                                        <th>Item Penilaian</th>
                                        <th>Catatan</th>
                                        <th>Rekomendasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($catatanSidang as $catatan)
                                        @php
                                            $item = $items->get($catatan->id_item_penilaian);
                                        @endphp
                                        <tr>
                                            <td>{{ $item->kode_item ?? 'Item' }} — {{ $item->nama_item ?? '' }}</td>
                                            <td>{{ $catatan->catatan ?: '-' }}</td>
                                            <td>{{ $catatan->rekomendasi ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Belum ada catatan hasil visitasi.</p>
                    @endif
                </div>
            </div>
            <div class="ettd-panel" id="recommendationTab" role="tabpanel">
                <div class="document-container">
                    <h3 style="color:#b34700; margin-bottom: 16px;">Rekomendasi Hasil Akreditasi</h3>
                    <p><a href="{{ route('ttd.sidang.rekomendasi.export', $token) }}" class="btn-ettd">Export DOCX</a>
                    </p>
                    <div class="meta">
                        <table>
                            <tr>
                                <td>Tahun Pengajuan</td>
                                <td>:</td>
                                <td>{{ $tahunPengajuan ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td>Jenis Pengajuan</td>
                                <td>:</td>
                                <td>{{ $jenisPengajuan }}</td>
                            </tr>
                            <tr>
                                <td>Nilai Final</td>
                                <td>:</td>
                                <td>{{ $nilaiFinal ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Predikat Final</td>
                                <td>:</td>
                                <td>{{ $predikatFinal ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    @php
                        $rekomendasiDipertahankan = $rekomendasiHasilAkreditasi->get('dipertahankan', collect());
                        $rekomendasiDiperbaiki = $rekomendasiHasilAkreditasi->get('diperbaiki', collect());
                    @endphp
                    <h4>Hal-hal yang harus dipertahankan</h4>
                    @if ($rekomendasiDipertahankan->isNotEmpty())
                        <ol>
                            @foreach ($rekomendasiDipertahankan as $rekomendasi)
                                <li>{{ $rekomendasi->isi }}</li>
                            @endforeach
                        </ol>
                    @else
                        <p class="text-muted">Belum ada rekomendasi hasil akreditasi.</p>
                    @endif
                    <h4>Hal-hal yang harus diperbaiki</h4>
                    @if ($rekomendasiDiperbaiki->isNotEmpty())
                        <ol>
                            @foreach ($rekomendasiDiperbaiki as $rekomendasi)
                                <li>{{ $rekomendasi->isi }}</li>
                            @endforeach
                        </ol>
                    @else
                        <p class="text-muted">Belum ada rekomendasi hasil akreditasi.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="ettdModal" class="ettd-modal-backdrop" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="ettd-modal-card">
            <div class="ettd-modal-header">
                <button type="button" class="ettd-modal-close" onclick="closeEttdModal()">&times;</button>
                <h3 id="ettdModalTitle" class="ettd-modal-title"></h3>
            </div>
            <div id="ettdModalBody" class="ettd-modal-body"></div>
            <div id="ettdModalFooter" class="ettd-modal-footer"></div>
        </div>
    </div>

    <!-- Modal tanda tangan -->
    <div class="modal" id="signatureModal">
        <div class="modal-content">
            <span class="close" onclick="closeSignature()">&times;</span>
            <div class="modal-header">
                <h2 id="modalTitle">Tanda Tangan</h2>
                <p>Pilih metode dan buat tanda tangan Anda</p>
            </div>

            <div class="signature-method-tabs">
                <button type="button" class="signature-method-tab active" data-method="draw">Gambar
                    Langsung</button>
                <button type="button" class="signature-method-tab" data-method="upload">Upload PNG</button>
            </div>

            <div id="drawMethodPanel" class="signature-method-panel active">
                <div class="canvas-container">
                    <canvas id="signatureCanvas" width="520" height="220"></canvas>
                </div>
                <div class="signature-method-actions">
                    <button type="button" class="btn btn-secondary" onclick="clearCanvas()">Hapus Gambar</button>
                    <button type="button" class="btn btn-primary" onclick="saveSignature()">Simpan</button>
                </div>
            </div>

            <div id="uploadMethodPanel" class="signature-method-panel">
                <p class="upload-help">Upload gambar tanda tangan dengan format <strong>.png</strong> dan ukuran
                    maksimal <strong>2 MB</strong>.</p>
                <div class="mb-3">
                    <label for="signatureFile" class="form-label">Pilih file PNG</label>
                    <input class="form-control form-control-sm" type="file" id="signatureFile"
                        accept=".png,image/png" onchange="previewSignatureFile(event)">
                </div>
                <div id="signatureUploadError" class="upload-error"></div>
                <div id="signatureUploadLoading" class="upload-loading" style="display:none;">
                    <span class="upload-loading-spinner"></span>
                    <span>Memuat gambar...</span>
                </div>
                <img id="signaturePreview" class="signature-preview" alt="Preview tanda tangan"
                    style="display:none;">
                <div class="signature-method-actions">
                    <button type="button" class="btn btn-secondary" onclick="clearUploadedSignature()">Hapus
                        Gambar</button>
                    <button type="button" class="btn btn-primary" onclick="saveSignature()">Simpan</button>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.querySelectorAll('.ettd-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.ettd-tab').forEach(item => item.classList.remove('active'));
                document.querySelectorAll('.ettd-panel').forEach(panel => panel.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab)?.classList.add('active');
            });
        });

        const token = '{{ $pengajuan->ttd_sidang_token }}';
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        let activeSigner = null;
        let drawing = false;
        let signatureInputMethod = 'draw';
        let signatures = {};
        const modal = document.getElementById('signatureModal');
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#4a3728';

        // Muat status tanda tangan yang sudah tersimpan dari server
        document.querySelectorAll('.signature-box[data-signed="1"]').forEach(box => {
            signatures[box.dataset.signer] = true;
        });

        document.querySelectorAll('.signature-method-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                signatureInputMethod = tab.dataset.method;
                document.querySelectorAll('.signature-method-tab').forEach(item => item.classList.remove(
                    'active'));
                document.querySelectorAll('.signature-method-panel').forEach(panel => panel.classList
                    .remove('active'));
                tab.classList.add('active');
                document.getElementById(signatureInputMethod + 'MethodPanel')?.classList.add('active');
            });
        });

        function point(e) {
            const r = canvas.getBoundingClientRect();
            const x = (e.touches ? e.touches[0].clientX : e.clientX) - r.left;
            const y = (e.touches ? e.touches[0].clientY : e.clientY) - r.top;
            return [x * canvas.width / r.width, y * canvas.height / r.height];
        }

        canvas.addEventListener('pointerdown', e => {
            drawing = true;
            ctx.beginPath();
            ctx.moveTo(...point(e));
        });
        canvas.addEventListener('pointermove', e => {
            if (drawing) {
                ctx.lineTo(...point(e));
                ctx.stroke();
            }
        });
        window.addEventListener('pointerup', () => drawing = false);

        function openSignature(type) {
            if (document.getElementById('beritaAcaraStatus')?.value === 'submitted') return;
            if (document.getElementById('sidangAssessmentStatus')?.value !== 'submitted') {
                showEttdModal('Penilaian Belum Disubmit',
                    '<p class="mb-0">Penilaian Sidang Majelis perlu disubmit oleh asesor terlebih dahulu sebelum melakukan tanda tangan Berita Acara Sidang.</p>',
                    '<button type="button" class="btn-ettd" onclick="closeEttdModal()">Tutup</button>');
                return;
            }
            activeSigner = type;
            document.getElementById('modalTitle').textContent = 'Tanda Tangan ' + type.replaceAll('_', ' ');
            resetSignatureModalState();
            modal.classList.add('show');
        }

        function resetSignatureModalState() {
            clearCanvas();
            clearUploadedSignature();
            document.querySelectorAll('.signature-method-tab').forEach(item => item.classList.toggle('active', item.dataset
                .method === 'draw'));
            document.querySelectorAll('.signature-method-panel').forEach(panel => panel.classList.toggle('active', panel
                .id === 'drawMethodPanel'));
            signatureInputMethod = 'draw';
        }

        function closeSignature() {
            modal.classList.remove('show');
            activeSigner = null;
            resetSignatureModalState();
        }

        function clearCanvas() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function clearUploadedSignature() {
            const fileInput = document.getElementById('signatureFile');
            if (fileInput) fileInput.value = '';
            document.getElementById('signatureUploadError').textContent = '';
            document.getElementById('signatureUploadLoading').style.display = 'none';
            document.getElementById('signaturePreview').removeAttribute('src');
            document.getElementById('signaturePreview').style.display = 'none';
        }

        function previewSignatureFile(event) {
            const file = event.target.files[0];
            const error = document.getElementById('signatureUploadError');
            const loading = document.getElementById('signatureUploadLoading');
            const preview = document.getElementById('signaturePreview');
            error.textContent = '';
            preview.style.display = 'none';
            preview.removeAttribute('src');
            if (!file) return;
            const isPng = file.type === 'image/png' || /\.png$/i.test(file.name);
            if (!isPng) {
                error.textContent = 'Format bukan .png';
                event.target.value = '';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                error.textContent = 'Ukuran file maksimal 2 MB.';
                event.target.value = '';
                return;
            }
            loading.style.display = 'flex';
            const reader = new FileReader();
            reader.onload = e => {
                loading.style.display = 'none';
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.onerror = () => {
                loading.style.display = 'none';
                error.textContent = 'Gagal membaca file.';
            };
            reader.readAsDataURL(file);
        }

        async function saveSignature() {
            if (!activeSigner) return;

            let signatureData;
            if (signatureInputMethod === 'upload') {
                const file = document.getElementById('signatureFile').files[0];
                if (!file || (file.type !== 'image/png' && !/\.png$/i.test(file.name))) {
                    document.getElementById('signatureUploadError').textContent = 'Format bukan .png';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    document.getElementById('signatureUploadError').textContent = 'Ukuran file maksimal 2 MB.';
                    return;
                }
                signatureData = await new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(file);
                });
            } else {
                const canvasData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const hasContent = canvasData.data.some(channel => channel !== 0);
                if (!hasContent) {
                    alert('Silakan buat tanda tangan terlebih dahulu!');
                    return;
                }
                signatureData = canvas.toDataURL('image/png');
            }

            const saveButtons = document.querySelectorAll('.signature-method-actions .btn-primary');
            saveButtons.forEach(btn => {
                btn.disabled = true;
                btn.dataset.originalText = btn.innerHTML;
                btn.innerHTML = 'Menyimpan...';
            });

            try {
                const response = await fetch('{{ route('ttd.sidang.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({
                        token,
                        signer_type: activeSigner,
                        signature_data: signatureData
                    })
                });
                const result = await response.json();
                if (!response.ok) {
                    alert(result.message || 'Gagal menyimpan tanda tangan');
                    return;
                }

                const savedSignatureUrl = result.data?.signature_url || signatureData;
                const signerType = activeSigner;
                const signatureBox = document.querySelector(`[data-signer="${signerType}"]`);
                const signerLabel = signatureBox?.closest('.signer')?.querySelector('h3')?.textContent ||
                    'Penandatangan';

                signatures[signerType] = true;

                if (signatureBox) {
                    signatureBox.style.transform = 'scale(0.9)';
                    signatureBox.style.transition = 'all 0.3s ease';
                    setTimeout(() => {
                        signatureBox.innerHTML =
                            `<img src="${savedSignatureUrl}" style="max-width:90%; max-height:100px; object-fit:contain;">`;
                        signatureBox.classList.add('signed');
                        signatureBox.dataset.signed = '1';
                        const hint = signatureBox.closest('.signer')?.querySelector('.hint');
                        if (hint) hint.textContent = 'Sudah ditandatangani';
                        signatureBox.style.transform = 'scale(1)';
                    }, 150);
                }

                closeSignature();
                showSignatureSuccessToast(signerLabel);
                setTimeout(() => updateSignatureCount(), 300);
            } finally {
                saveButtons.forEach(btn => {
                    btn.disabled = false;
                    if (btn.dataset.originalText) btn.innerHTML = btn.dataset.originalText;
                });
            }
        }

        function showSignatureSuccessToast(signerLabel) {
            const toast = document.createElement('div');
            toast.className = 'signature-toast';
            toast.innerHTML =
                `<div style="font-weight:bold;">&#10003; Tanda Tangan Berhasil</div><div style="font-size:12px; opacity:0.9;">${signerLabel} telah menandatangani dokumen</div>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'slideOutRight 0.5s ease-in';
                setTimeout(() => toast.remove(), 500);
            }, 3500);
        }

        function updateSignatureCount() {
            const signedCount = Object.keys(signatures).length;
            const statusElement = document.getElementById('statusText');
            const statusContainer = document.getElementById('status');
            const submitControls = document.getElementById('submitControls');
            const beritaAcaraStatus = document.getElementById('beritaAcaraStatus')?.value;
            const submitBaBtn = document.getElementById('submitBaBtn');
            const resetBaBtn = document.getElementById('resetBaBtn');
            const canManage = document.getElementById('canManage')?.value === '1';
            const assessmentSubmitted = document.getElementById('sidangAssessmentStatus')?.value === 'submitted';

            if (!assessmentSubmitted) {
                statusElement.textContent = 'Penilaian Sidang Majelis belum disubmit oleh asesor.';
                statusContainer.className = 'status-info status-incomplete';
                submitControls.style.display = 'none';
                return;
            }

            if (beritaAcaraStatus === 'submitted') {
                if (submitBaBtn) submitBaBtn.remove();
                const resetAllBtn = document.getElementById('resetAllSignaturesBtn');
                if (resetAllBtn) resetAllBtn.remove();
                if (resetBaBtn) resetBaBtn.style.display = canManage ? 'inline-block' : 'none';
                statusElement.textContent = 'Berita Acara Sidang telah disubmit. Semua tanda tangan lengkap.';
                statusContainer.className = 'status-info status-complete';
                submitControls.style.display = 'flex';
                return;
            }

            if (signedCount > 0) {
                statusContainer.className = 'status-info';
                statusElement.textContent = `Tanda tangan tersimpan (${signedCount}/3 selesai)`;

                if (signedCount === 3) {
                    statusElement.textContent = 'Semua tanda tangan telah lengkap! Silakan submit dokumen.';
                    submitControls.style.display = 'flex';
                    const resetAllBtn = document.getElementById('resetAllSignaturesBtn');
                    if (resetAllBtn && canManage) resetAllBtn.style.display = 'inline-block';
                } else {
                    submitControls.style.display = 'none';
                }
            } else {
                statusContainer.className = 'status-info status-incomplete';
                submitControls.style.display = 'none';
            }
        }

        async function submitDocument() {
            if (document.getElementById('beritaAcaraStatus')?.value === 'submitted') {
                return;
            }
            const signedCount = Object.keys(signatures).length;

            if (signedCount !== 3) {
                alert('Semua tanda tangan harus lengkap sebelum submit!');
                return;
            }

            const response = await fetch('{{ route('ttd.sidang.submit.ba') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    token
                })
            });
            const result = await response.json();
            if (!response.ok) {
                alert(result.message || 'Berita Acara Sidang gagal disubmit.');
                return;
            }
            document.getElementById('beritaAcaraStatus').value = 'submitted';

            const statusElement = document.getElementById('statusText');
            const statusContainer = document.getElementById('status');
            const submitControls = document.getElementById('submitControls');

            statusElement.textContent = 'BERITA ACARA SIDANG BERHASIL DITANDATANGANI';
            statusContainer.className = 'status-info status-complete';

            if (document.getElementById('submitBaBtn')) document.getElementById('submitBaBtn').style.display = 'none';
            if (document.getElementById('resetAllSignaturesBtn')) document.getElementById('resetAllSignaturesBtn')
                .remove();
            if (document.getElementById('resetBaBtn')) {
                document.getElementById('resetBaBtn').style.display = document.getElementById('canManage')?.value ===
                    '1' ? 'inline-block' : 'none';
            }
            submitControls.style.display = 'flex';

            // Trigger perayaan setelah jeda singkat
            setTimeout(() => {
                triggerCelebration();
            }, 500);

            // Nonaktifkan seluruh kotak tanda tangan agar tidak bisa diedit lagi
            document.querySelectorAll('.signature-box').forEach(box => {
                box.style.pointerEvents = 'none';
                box.style.opacity = '0.9';
                box.onclick = null;
            });

            // Tidak ada redirect otomatis - pengguna tetap di halaman ini setelah animasi
        }

        function confirmResetAllSignatures() {
            return new Promise(resolve => {
                showEttdModal('Reset Tanda Tangan',
                    '<p class="mb-0">Reset semua tanda tangan sidang?</p><p class="text-muted mt-2 mb-0">Semua tanda tangan yang tersimpan akan dihapus dan harus dibuat ulang.</p>',
                    '<button type="button" class="btn-ettd-reset" id="cancelResetBtn">Tidak</button><button type="button" class="btn-ettd" id="confirmResetBtn">Ya, Reset</button>'
                );
                document.getElementById('cancelResetBtn').onclick = () => {
                    closeEttdModal();
                    resolve(false);
                };
                document.getElementById('confirmResetBtn').onclick = () => {
                    closeEttdModal();
                    resolve(true);
                };
            });
        }

        async function resetAllSignatures() {
            if (document.getElementById('canManage')?.value !== '1') return;
            if (!await confirmResetAllSignatures()) return;

            const response = await fetch('{{ route('ttd.sidang.reset.all') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    token
                })
            });
            const result = await response.json();
            if (!response.ok) {
                alert(result.message || 'Reset tanda tangan gagal.');
                return;
            }
            window.location.reload();
        }

        function confirmResetBeritaAcara() {
            return new Promise(resolve => {
                showEttdModal('Reset Berita Acara Sidang',
                    '<p class="mb-0">Reset Berita Acara Sidang?</p><p class="text-muted mt-2 mb-0">Berita acara akan dibuka kembali sehingga tanda tangan dapat diperbaiki. Tanda tangan yang sudah tersimpan tidak akan dihapus.</p>',
                    '<button type="button" class="btn-ettd-reset" id="cancelResetBaBtn">Tidak</button><button type="button" class="btn-ettd" id="confirmResetBaBtn">Ya, Reset</button>'
                );
                document.getElementById('cancelResetBaBtn').onclick = () => {
                    closeEttdModal();
                    resolve(false);
                };
                document.getElementById('confirmResetBaBtn').onclick = () => {
                    closeEttdModal();
                    resolve(true);
                };
            });
        }

        async function resetBeritaAcara() {
            if (document.getElementById('canManage')?.value !== '1') return;
            if (!await confirmResetBeritaAcara()) return;

            const response = await fetch('{{ route('ttd.sidang.reset.ba') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({
                    token
                })
            });
            const result = await response.json();
            if (!response.ok) {
                showEttdModal('Gagal', '<p>' + (result.message || 'Reset Berita Acara Sidang gagal.') + '</p>',
                    '<button type="button" class="btn-ettd" onclick="closeEttdModal()">Tutup</button>');
                return;
            }
            window.location.reload();
        }

        function triggerCelebration() {
            const overlay = document.createElement('div');
            overlay.className = 'celebration-overlay';
            document.body.appendChild(overlay);

            for (let i = 0; i < 60; i++) {
                const piece = document.createElement('div');
                piece.className = 'confetti';
                piece.style.left = Math.random() * 100 + '%';
                piece.style.animationDelay = (Math.random() * 1.5) + 's';
                piece.style.animationDuration = (2 + Math.random() * 2) + 's';
                overlay.appendChild(piece);
            }

            const message = document.createElement('div');
            message.className = 'celebration-message';
            message.textContent = 'BERITA ACARA SIDANG BERHASIL DITANDATANGANI!';
            document.body.appendChild(message);

            setTimeout(() => {
                overlay.remove();
                message.remove();
            }, 4000);
        }

        window.addEventListener('load', updateSignatureCount);

        // Modal generik (share link)
        function showEttdModal(title, bodyHtml, footerHtml) {
            document.getElementById('ettdModalTitle').textContent = title;
            document.getElementById('ettdModalBody').innerHTML = bodyHtml;
            document.getElementById('ettdModalFooter').innerHTML = footerHtml;
            const ettdModal = document.getElementById('ettdModal');
            ettdModal.classList.add('show');
            ettdModal.setAttribute('aria-hidden', 'false');
        }

        function closeEttdModal() {
            const ettdModal = document.getElementById('ettdModal');
            ettdModal.classList.remove('show');
            ettdModal.setAttribute('aria-hidden', 'true');
        }

        function shareDocument() {
            if (!token) {
                showEttdModal('Link Tidak Tersedia', '<p class="mb-0">Token sidang tidak ditemukan.</p>',
                    '<button type="button" class="btn-ettd" onclick="closeEttdModal()">Tutup</button>');
                return;
            }
            const shareUrl = window.location.href;
            showEttdModal('Bagikan Link',
                `<p class="mb-0">Salin link berikut untuk membagikan halaman tanda tangan sidang:</p><input id="ettdShareInput" class="ettd-share-input mt-3" type="text" value="${shareUrl}" readonly onclick="this.select()">`,
                `<button type="button" class="btn-ettd" id="copyShareBtn">Copy Link</button> <button type="button" class="btn-ettd-reset" onclick="closeEttdModal()">Tutup</button>`
            );
            document.getElementById('copyShareBtn').onclick = () => copyShareLink(shareUrl);
        }

        async function copyShareLink(shareUrl) {
            const input = document.getElementById('ettdShareInput');
            try {
                await navigator.clipboard.writeText(shareUrl);
            } catch (error) {
                input.select();
                document.execCommand('copy');
            }
            showEttdModal('Link Berhasil Disalin',
                `<p class="mb-0">Link berhasil disalin ke clipboard.</p><input class="ettd-share-input mt-3" type="text" value="${shareUrl}" readonly>`,
                `<button type="button" class="btn-ettd" onclick="closeEttdModal()">Tutup</button>`);
        }

        // Tutup modal saat klik di luar area konten
        window.onclick = function(event) {
            if (event.target === modal) closeSignature();
            if (event.target === document.getElementById('ettdModal')) closeEttdModal();
        };
    </script>
</body>

</html>
