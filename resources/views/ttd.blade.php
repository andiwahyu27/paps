<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tanda Tangan Berita Acara Akreditasi PSTK 2026</title>
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
            max-width: 1200px;
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

        .header-logo {
            position: absolute;
            top: 18px;
            left: 22px;
            width: 110px;
            height: 110px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            z-index: 2;
        }

        .header-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        @media (max-width: 560px) {
            .header-logo {
                width: 44px;
                height: 44px;
                top: 14px;
                left: 14px;
            }
        }

        .main-content {
            padding: 30px;
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

        .ettd-modal-backdrop.show { display: flex; }

        .ettd-modal-card {
            width: min(100%, 520px);
            background: #fffaf5;
            border: 1px solid #e8c9a8;
            border-radius: 12px;
            box-shadow: 0 12px 35px rgba(74, 55, 40, 0.25);
            overflow: hidden;
        }

        .ettd-modal-header, .ettd-modal-footer {
            padding: 16px 20px;
            background: #fff0e0;
        }

        .ettd-modal-header { border-bottom: 1px solid #e8c9a8; }
        .ettd-modal-footer { border-top: 1px solid #e8c9a8; text-align: right; }
        .ettd-modal-body { padding: 20px; color: #4a3728; }
        .ettd-modal-title { margin: 0; color: #8a5a2b; font-size: 1.15rem; }
        .ettd-modal-close { border: 0; background: transparent; color: #8a5a2b; font-size: 1.5rem; float: right; cursor: pointer; }
        .ettd-share-input { width: 100%; padding: 10px; border: 1px solid #e8c9a8; border-radius: 6px; color: #4a3728; background: #fff; }
        .ettd-modal-footer .btn + .btn { margin-left: 8px; }

        .document-container {
            background: #fffaf5;
            border: 1px solid #ffe0b2;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
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

        .document-title {
            text-align: center;
            font-size: 1.7em;
            font-weight: bold;
            margin-bottom: 6px;
            color: #b34700;
            letter-spacing: 0.3px;
        }

        .document-title-underline {
            width: 1000px;
            height: 4px;
            background: #f7941d;
            border-radius: 4px;
            margin: 0 auto 24px auto;
        }

        .document-content {
            line-height: 1.8;
            color: #4a3728;
            margin-bottom: 30px;
        }

        .document-content ol {
            background: #fff6ec;
            border-left: 4px solid #f7941d;
            border-radius: 6px;
            padding: 16px 16px 16px 36px;
        }

        .document-content strong {
            color: #b34700;
        }

        .signature-block {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
            align-items: start;
        }

        .signature-col-title {
            font-weight: 700;
            color: #b34700;
            text-align: left;
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 2px solid #ffd699;
        }

        .signer-row {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 22px;
            text-align: left;
        }

        .signer-row:last-child {
            margin-bottom: 0;
        }

        .signer-label {
            min-width: 120px;
            font-size: 0.85em;
            color: #8a5a2b;
        }

        .signer-name {
            font-weight: bold;
            color: #2c2c2c;
        }

        .signature-box {
            width: 170px;
            height: 90px;
            flex-shrink: 0;
            border: 2px dashed #f7941d;
            border-radius: 8px;
            position: relative;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .signature-box:hover {
            border-color: #d35400;
            box-shadow: 0 3px 10px rgba(247, 148, 29, 0.25);
        }

        .signature-box.signed {
            border: 2px solid #ef6c00;
            border-style: solid;
            background: #fffaf3;
        }

        .signature-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #b98a55;
            font-size: 0.8em;
            text-align: center;
            width: 90%;
            pointer-events: none;
        }

        .signature-name {
            margin-top: 4px;
            font-weight: bold;
            color: #2c2c2c;
        }

        .kepala-block {
            text-align: center;
        }

        .kepala-block .signature-box {
            margin: 16px auto;
        }

        .controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
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

        .btn-success {
            background: #ef6c00;
            color: white;
            box-shadow: 0 4px 12px rgba(239, 108, 0, 0.35);
        }

        .btn-success:hover {
            background: #d35400;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(74, 40, 10, 0.55);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
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

        .canvas-container {
            border: 2px solid #f7941d;
            border-radius: 10px;
            margin: 20px 0;
            background: white;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a3728;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #f0e0cc;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.2s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #f7941d;
            box-shadow: 0 0 0 3px rgba(247, 148, 29, 0.12);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .signature-form {
            background: #fff6ec;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .signature-form h3 {
            margin-bottom: 15px;
            color: #b34700;
            text-align: center;
        }

        .signature-canvas-modal {
            display: block;
            border-radius: 8px;
        }

        .modal-controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
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

        .status-info {
            background: #fff6ec;
            border: 1px solid #ffd699;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            color: #4a3728;
        }

        .status-incomplete {
            background: #fff3cd;
            border: 1px solid #ffc107;
        }

        /* Celebration Effects - orange palette, simplified */
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

        .sparkle {
            position: absolute;
            background: #ffd699;
            border-radius: 50%;
            animation: sparkle 1.5s ease-in-out infinite;
        }

        @keyframes sparkle {

            0%,
            100% {
                opacity: 0;
                transform: scale(0);
            }

            50% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .status-complete {
            background: #ef6c00;
            border: 2px solid #d35400;
            color: white;
        }

        @media (max-width: 768px) {
            .signature-block {
                grid-template-columns: 1fr;
                gap: 28px;
            }

            .signer-row {
                flex-wrap: wrap;
            }

            .controls {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-tag">BERITA ACARA AKREDITASI PSTK {{ $tahunPengajuan }}</div>
            <h1>PENANDATANGANAN DIGITAL</h1>
            <p>{{ strtoupper($namaLembaga ?? 'LEMBAGA BELUM DITENTUKAN') }}</p>
        </div>

        <div class="main-content">
            <div class="ettd-tabs" role="tablist">
                <button class="ettd-tab active" type="button" role="tab" data-tab="signatureTab">Tanda Tangan</button>
                <button class="ettd-tab" type="button" role="tab" data-tab="notesTab">Catatan</button>
            </div>
            <div class="ettd-panel active" id="signatureTab" role="tabpanel">
            <div class="document-container">
                <button class="share-button" onclick="shareDocument()">
                    Share Link
                </button>
                <div class="document-title">
                    BERITA ACARA<br>
                    VISITASI TIM ASESOR AKREDITASI<br>
                    {{ strtoupper($namaLembaga ?? 'LEMBAGA BELUM DITENTUKAN') }}
                </div>
                <div class="document-title-underline"></div>

                <div class="document-content">
                    <p>Pada hari ini, <strong>{{ $customDateTime ?? 'Belum ditentukan' }}</strong>,
                        telah diselenggarakan visitasi dalam rangka verifikasi data Akreditasi Lembaga Penyelenggara
                        Pelatihan di Bidang Sistem Teknologi Berbasis Komputer di
                        {{ $namaLembaga ?? 'Belum ditentukan' }}
                        oleh:</p>

                    <ol style="margin: 20px 0; padding-left: 30px;">
                        <li><strong>Nama:</strong>
                            {{ $asesorData['asesor1']['name'] ?? 'Belum ditentukan' }}<br><strong>Jabatan:</strong>
                            {{ $asesorData['asesor1']['title'] ?? 'Ketua Tim Asesor' }}</li>
                        <li><strong>Nama:</strong>
                            {{ $asesorData['asesor2']['name'] ?? 'Belum ditentukan' }}<br><strong>Jabatan:</strong>
                            {{ $asesorData['asesor2']['title'] ?? 'Anggota Tim Asesor' }}
                        </li>
                        <li><strong>Nama:</strong>
                            {{ $asesorData['asesor3']['name'] ?? 'Belum ditentukan' }}<br><strong>Jabatan:</strong>
                            {{ $asesorData['asesor3']['title'] ?? 'Anggota Tim Asesor' }}</li>
                    </ol>

                    <p style="margin-top: 16px;">Berdasarkan hasil visitasi dan verifikasi terhadap bahan/dokumen,
                        Tim Asesor memberikan hasil rekomendasi untuk memenuhi persyaratan Akreditasi sebagaimana
                        catatan terlampir.</p>

                    <p style="margin-top: 16px;"><strong>Demikian, Berita Acara ini dibuat untuk dapat diketahui
                            bersama:</strong></p>
                </div>

                <div class="signature-block">
                    <!-- Kolom kiri: Ketua + Anggota, rata kiri, sejajar -->
                    <div class="signature-col signature-col-left">
                        <div class="signature-col-title">Tim Asesor</div>

                        <div class="signer-row">
                            <div class="signature-box" onclick="openSignatureModal('asesor1')">
                                <div class="signature-placeholder">Klik untuk tanda tangan</div>
                            </div>
                            <div>
                                <div class="signer-label">Ketua Tim Asesor</div>
                                <div class="signature-name">{{ $asesorData['asesor1']['name'] ?? 'Belum ditentukan' }}</div>
                            </div>
                        </div>

                        <div class="signer-row">
                            <div class="member-signature-box signature-box" onclick="openSignatureModal('asesor2')">
                                <div class="signature-placeholder">Klik untuk tanda tangan</div>
                            </div>
                            <div>
                                <div class="signer-label">Anggota Tim Asesor</div>
                                <div class="signature-name">{{ $asesorData['asesor2']['name'] ?? 'Belum ditentukan' }}</div>
                            </div>
                        </div>

                        <div class="signer-row">
                            <div class="member-signature-box signature-box" onclick="openSignatureModal('asesor3')">
                                <div class="signature-placeholder">Klik untuk tanda tangan</div>
                            </div>
                            <div>
                                <div class="signer-label">Anggota Tim Asesor</div>
                                <div class="signature-name">{{ $asesorData['asesor3']['name'] ?? 'Belum ditentukan' }}</div>
                            </div>
                        </div>

                        <div style="margin-top: 14px; color: #8a5a2b; font-size: 0.9em;">
                            {{ $signatureDate ?? 'Jakarta, 24 Juni ' . date('Y') }}
                        </div>
                    </div>

                    <!-- Kolom kanan: Kepala Lembaga -->
                    <div class="signature-col kepala-block">
                        <div class="signature-col-title" style="text-align:center; border-bottom:none;">
                            {{ $leaderData['title'] ?? 'Kepala ' . ($namaLembaga ?? 'Lembaga Belum Ditentukan') }}
                        </div>
                        <div class="signature-box" onclick="openSignatureModal('kepala')">
                            <div class="signature-placeholder">Klik untuk tanda tangan</div>
                        </div>
                        <div class="signature-name">{{ $leaderData['name'] ?? 'Belum ditentukan' }}</div>
                    </div>
                </div>
            </div>

            <div id="status" class="status-info status-incomplete">
                <strong>Status:</strong> <span id="statusText">Menunggu tanda tangan (0/4 selesai)</span>
            </div>

            <div class="controls" id="submitControls" style="display: none; text-align: center;">
                @if($isSekretariat && !$baSubmitted)
                    <button class="btn btn-success" id="submitBaBtn" onclick="submitDocument()">SUBMIT BERITA ACARA</button>
                    <button class="btn btn-warning" id="resetAllSignaturesBtn" onclick="resetAllSignatures()" style="display:none;">RESET TANDA TANGAN</button>
                @endif
                <button class="btn btn-danger" id="resetBaBtn" onclick="resetBeritaAcara()" style="display:none;">RESET BERITA ACARA</button>
            </div>

                </div>
                <div class="ettd-panel" id="notesTab" role="tabpanel">
                    <div class="document-container">
                        <h3>Catatan Hasil Visitasi</h3>
                        @if($catatanVisitasi->isNotEmpty())
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
                                        @foreach($catatanVisitasi as $catatan)
                                            @php($item = $items->get($catatan->id_item_penilaian))
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

    <!-- Modal untuk tanda tangan -->
    <div id="signatureModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeSignatureModal()">&times;</span>
            <div class="modal-header">
                <h2>Buat Tanda Tangan Digital</h2>
                <p>Lengkapi data dan buat tanda tangan Anda</p>
            </div>

            <!-- Form Data Penandatangan -->
            <div class="signature-form">
                <h3>Data Penandatangan</h3>
                <form id="signatureForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="signerName">Nama Lengkap *</label>
                            <input type="text" id="signerName" name="signer_name" disabled required
                                placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="form-group">
                            <label for="signerTitle">Jabatan *</label>
                            <input type="text" id="signerTitle" name="signer_title" disabled required
                                placeholder="Masukkan jabatan">
                        </div>
                    </div>
                    <input type="hidden" id="signerType" name="jenis_user">
                    <input type="hidden" id="ttdToken" name="token" value="{{ $pengajuan->ttd_token }}">
                    <input type="hidden" id="beritaAcaraStatus" value="{{ $baSubmitted ? 'submitted' : '' }}">
                    <input type="hidden" id="isSekretariat" value="{{ $isSekretariat ? '1' : '0' }}">
                </form>
            </div>

            <!-- Canvas Tanda Tangan -->
            <div class="modal-header">
                <h3>Area Tanda Tangan</h3>
                <p>Gambar tanda tangan Anda di area di bawah ini</p>
            </div>
            <div class="canvas-container">
                <canvas id="signatureCanvas" class="signature-canvas-modal" width="540" height="200"></canvas>
            </div>

            <div class="modal-controls">
                <button class="btn btn-secondary" onclick="clearCanvas()">Hapus</button>
                <button class="btn btn-primary" onclick="saveSignature()">Simpan</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        document.querySelectorAll('.ettd-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.ettd-tab').forEach(item => item.classList.remove('active'));
                document.querySelectorAll('.ettd-panel').forEach(panel => panel.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab)?.classList.add('active');
            });
        });

        const ttdToken = @json($pengajuan->ttd_token);
        let canvas, ctx;
        let isDrawing = false;
        let currentSignatureTarget = null;
        let signatures = {};
        let signatureCount = 0;

        function initCanvas() {
            canvas = document.getElementById('signatureCanvas');
            ctx = canvas.getContext('2d');

            // Set canvas style
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#4a3728';

            // Mouse events
            canvas.addEventListener('mousedown', startDrawing);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseup', stopDrawing);
            canvas.addEventListener('mouseout', stopDrawing);

            // Touch events
            canvas.addEventListener('touchstart', handleTouch);
            canvas.addEventListener('touchmove', handleTouch);
            canvas.addEventListener('touchend', stopDrawing);
        }

        function clearCanvas() {
            if (canvas && ctx) {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        }

        function startDrawing(e) {
            isDrawing = true;
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            ctx.beginPath();
            ctx.moveTo(x, y);
        }

        function draw(e) {
            if (!isDrawing) return;
            const rect = canvas.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            ctx.lineTo(x, y);
            ctx.stroke();
        }

        function stopDrawing() {
            isDrawing = false;
        }

        function handleTouch(e) {
            e.preventDefault();
            const touch = e.touches[0];
            const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' :
                e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
            canvas.dispatchEvent(mouseEvent);
        }

        function openSignatureModal(target) {
            if (document.getElementById('beritaAcaraStatus')?.value === 'submitted') {
                return;
            }
            currentSignatureTarget = target;
            document.getElementById('signatureModal').style.display = 'block';

            // Set signer type
            document.getElementById('signerType').value = target;

            // Pre-fill data based on target
            const signerData = getSignerData(target);
            document.getElementById('signerName').value = signerData.name;
            document.getElementById('signerTitle').value = signerData.title;

            // Check if this signature already exists
            const isReplacement = signatures[target];
            if (isReplacement) {
                // Show replacement message
                const modalHeader = document.querySelector('.modal-header p');
                modalHeader.textContent =
                    'Mengganti tanda tangan yang sudah ada. Tanda tangan baru akan menimpa yang lama.';
                modalHeader.style.color = '#d35400';
            } else {
                // Reset to original message
                const modalHeader = document.querySelector('.modal-header p');
                modalHeader.textContent = 'Lengkapi data dan buat tanda tangan Anda';
                modalHeader.style.color = '';
            }

            // Initialize canvas and clear it immediately
            setTimeout(() => {
                initCanvas();
                // ALWAYS clear the canvas when opening modal - this is the fix
                clearCanvas();
            }, 100);
        }

        function getSignerData(target) {
            const signerMap = {
                'asesor1': {
                    name: '{{ $asesorData['asesor1']['name'] ?? 'Belum ditentukan' }}',
                    title: '{{ $asesorData['asesor1']['title'] ?? 'Ketua Tim Asesor' }}'
                },
                'kepala': {
                    name: '{{ $leaderData['name'] ?? 'Belum ditentukan' }}',
                    title: '{{ $leaderData['title'] ?? 'Kepala ' . ($namaLembaga ?? 'Lembaga Belum Ditentukan') }}'
                },
                'asesor2': {
                    name: '{{ $asesorData['asesor2']['name'] ?? 'Belum ditentukan' }}',
                    title: '{{ $asesorData['asesor2']['title'] ?? 'Anggota Tim Asesor' }}'
                },
                'asesor3': {
                    name: '{{ $asesorData['asesor3']['name'] ?? 'Belum ditentukan' }}',
                    title: '{{ $asesorData['asesor3']['title'] ?? 'Anggota Tim Asesor' }}'
                }
            };
            return signerMap[target] || {
                name: '',
                title: ''
            };
        }

        function closeSignatureModal() {
            document.getElementById('signatureModal').style.display = 'none';
            currentSignatureTarget = null;

            // Reset form
            document.getElementById('signatureForm').reset();
            clearCanvas();
        }

        async function saveSignature() {
            if (!currentSignatureTarget) return;

            // Validate form data
            const signerName = document.getElementById('signerName').value.trim();
            const signerTitle = document.getElementById('signerTitle').value.trim();
            const signerType = document.getElementById('signerType').value;
            if (!signerName || !signerTitle) {
                alert('Nama dan jabatan harus diisi!');
                return;
            }

            // Check if canvas has any content
            const canvasData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const hasContent = canvasData.data.some(channel => channel !== 0);

            if (!hasContent) {
                alert('Silakan buat tanda tangan terlebih dahulu!');
                return;
            }

            const signatureData = canvas.toDataURL();
            const currentDateTime = new Date();
            const tglSurat = currentDateTime.toISOString().split('T')[0];
            const waktuSurat = currentDateTime.toTimeString().split(' ')[0];

            // Show loading state
            const saveBtn = document.querySelector('.modal-controls .btn-primary');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = 'Menyimpan...';
            saveBtn.disabled = true;

            try {
                // Submit to server
                const response = await fetch('/ettd/save-signature', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        signer_type: signerType,
                        token: ttdToken,
                        signature_data: signatureData
                    })
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    // Get the saved signature URL from server response
                    const savedSignatureUrl = result.data.signature_url || signatureData;

                    // Store signature data locally
                    signatures[currentSignatureTarget] = {
                        data: savedSignatureUrl,
                        name: signerName,
                        title: signerTitle,
                        signed_at: result.data.signed_at || new Date().toISOString()
                    };

                    // Update the signature box with animation
                    const signatureBox = document.querySelector(
                        `[onclick="openSignatureModal('${currentSignatureTarget}')"]`);
                    signatureBox.style.transform = 'scale(0.9)';
                    signatureBox.style.transition = 'all 0.3s ease';

                    setTimeout(() => {
                        signatureBox.innerHTML =
                            `<img src="${savedSignatureUrl}" style="width: 100%; height: 100%; object-fit: contain;">`;
                        signatureBox.classList.add('signed');
                        signatureBox.style.transform = 'scale(1)';

                        // Small confirmation mark on the signature box
                        const miniCelebration = document.createElement('div');
                        miniCelebration.innerHTML = '&#10003;';
                        miniCelebration.style.position = 'absolute';
                        miniCelebration.style.top = '-10px';
                        miniCelebration.style.right = '-10px';
                        miniCelebration.style.width = '22px';
                        miniCelebration.style.height = '22px';
                        miniCelebration.style.lineHeight = '22px';
                        miniCelebration.style.textAlign = 'center';
                        miniCelebration.style.borderRadius = '50%';
                        miniCelebration.style.background = '#ef6c00';
                        miniCelebration.style.color = '#fff';
                        miniCelebration.style.fontSize = '13px';
                        miniCelebration.style.zIndex = '1000';
                        signatureBox.style.position = 'relative';
                        signatureBox.appendChild(miniCelebration);

                    }, 150);

                    // Update signature count
                    setTimeout(() => {
                        updateSignatureCount();
                    }, 300);

                    closeSignatureModal();
                } else {
                    throw new Error(result.message || 'Gagal menyimpan tanda tangan');
                }
            } catch (error) {
                console.error('Error saving signature:', error);
                alert('Terjadi kesalahan saat menyimpan tanda tangan: ' + error.message);
            } finally {
                // Reset button state
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
            }
        }

        function updateSignatureCount() {
            const signedCount = Object.keys(signatures).length;
            const statusElement = document.getElementById('statusText');
            const statusContainer = document.getElementById('status');
            const submitControls = document.getElementById('submitControls');
            const beritaAcaraStatus = document.getElementById('beritaAcaraStatus')?.value;
            const submitBaBtn = document.getElementById('submitBaBtn');
            const resetBaBtn = document.getElementById('resetBaBtn');

            if (beritaAcaraStatus === 'submitted') {
                if (submitBaBtn) submitBaBtn.remove();
                const resetAllBtn = document.getElementById('resetAllSignaturesBtn');
                if (resetAllBtn) resetAllBtn.remove();
                if (resetBaBtn) resetBaBtn.style.display = document.getElementById('isSekretariat')?.value === '1' ? 'inline-block' : 'none';
                statusElement.textContent = 'Berita Acara telah disubmit. Semua tanda tangan lengkap.';
                statusContainer.className = 'status-info status-complete';
                submitControls.style.display = 'flex';
                return;
            }

            statusElement.textContent = `Tanda tangan tersimpan (${signedCount}/4 selesai)`;

            if (signedCount > 0) {
                statusContainer.className = 'status-info';
                if (signedCount === 4) {
                    statusElement.textContent = 'Semua tanda tangan telah lengkap! Silakan submit dokumen.';
                    statusContainer.className = 'status-info';
                    statusContainer.style.background = '#fff0e0';
                    statusContainer.style.border = '1px solid #f7941d';
                    statusContainer.style.color = '#b34700';

                    // Show submit and reset-signature actions only when all four are signed
                    submitControls.style.display = 'flex';
                    const resetAllBtn = document.getElementById('resetAllSignaturesBtn');
                    if (resetAllBtn && document.getElementById('isSekretariat')?.value === '1') {
                        resetAllBtn.style.display = 'inline-block';
                    }
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

            if (signedCount !== 4) {
                alert('Semua tanda tangan harus lengkap sebelum submit!');
                return;
            }

            const response = await fetch('{{ route('ttd.submit.ba') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ token: ttdToken })
            });
            const result = await response.json();
            if (!response.ok) {
                alert(result.message || 'Berita Acara gagal disubmit.');
                return;
            }
            document.getElementById('beritaAcaraStatus').value = 'submitted';

            // Update status to submitted
            const statusElement = document.getElementById('statusText');
            const statusContainer = document.getElementById('status');
            const submitControls = document.getElementById('submitControls');

            statusElement.textContent = 'DOKUMEN BERHASIL DITANDATANGANI';
            statusContainer.className = 'status-info status-complete';

            // Replace Submit with Reset (only Secretariat may see Reset)
            if (document.getElementById('submitBaBtn')) document.getElementById('submitBaBtn').style.display = 'none';
            if (document.getElementById('resetBaBtn')) {
                document.getElementById('resetBaBtn').style.display = document.getElementById('isSekretariat')?.value === '1' ? 'inline-block' : 'none';
            }
            submitControls.style.display = 'flex';

            // Trigger celebration with slight delay
            setTimeout(() => {
                triggerCelebration();
            }, 500);

            // Disable all signature boxes to prevent further editing
            const allSignatureBoxes = document.querySelectorAll('.signature-box');
            allSignatureBoxes.forEach(box => {
                box.style.pointerEvents = 'none';
                box.style.opacity = '0.9';
                box.onclick = null;
            });

            // No automatic redirect - user stays on current page after celebration
        }

        function confirmResetAllSignatures() {
            return new Promise(resolve => {
                showEttdModal('Reset Tanda Tangan', '<p class="mb-0">Reset semua tanda tangan?</p><p class="text-muted mt-2 mb-0">Semua tanda tangan yang tersimpan akan dihapus dan harus dibuat ulang.</p>', '<button type="button" class="btn btn-secondary" id="cancelResetBtn">Tidak</button><button type="button" class="btn btn-danger" id="confirmResetBtn">Ya, Reset</button>');
                document.getElementById('cancelResetBtn').onclick = () => { closeEttdModal(); resolve(false); };
                document.getElementById('confirmResetBtn').onclick = () => { closeEttdModal(); resolve(true); };
            });
        }

        async function resetAllSignatures() {
            if (document.getElementById('isSekretariat')?.value !== '1') return;
            if (!await confirmResetAllSignatures()) return;

            const response = await fetch('{{ route('ttd.reset.all') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ token: ttdToken })
            });
            const result = await response.json();
            if (!response.ok) {
                alert(result.message || 'Reset tanda tangan gagal.');
                return;
            }
            window.location.reload();
        }

        async function resetBeritaAcara() {
            if (document.getElementById('isSekretariat')?.value !== '1') return;
            if (!confirm('Reset Berita Acara?\n\nBerita acara akan dibuka kembali sehingga tanda tangan dapat diperbaiki. Tanda tangan yang sudah tersimpan tidak akan dihapus.')) return;

            const response = await fetch('{{ route('ttd.reset.ba') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ token: ttdToken })
            });
            const result = await response.json();
            if (!response.ok) {
                alert(result.message || 'Reset Berita Acara gagal.');
                return;
            }
            window.location.reload();
        }

        function triggerCelebration() {
            // Create celebration overlay
            const overlay = document.createElement('div');
            overlay.className = 'celebration-overlay';
            document.body.appendChild(overlay);

            // Create celebration message
            const message = document.createElement('div');
            message.className = 'celebration-message';
            message.innerHTML = `
                Berhasil!<br>
                <span style="font-size: 0.6em;">Berita Acara Berhasil Ditandatangani</span><br>
                <span style="font-size: 0.45em;">Akreditasi PSTK 2026 Selesai</span>
            `;
            message.style.display = 'block';
            message.style.opacity = '1';
            document.body.appendChild(message);

            // Create confetti (kept light and orange-toned)
            for (let i = 0; i < 60; i++) {
                setTimeout(() => {
                    createConfetti(overlay);
                }, i * 60);
            }

            // Create sparkles around signed boxes
            const signatureBoxes = document.querySelectorAll('.signature-box.signed');
            signatureBoxes.forEach((box, index) => {
                setTimeout(() => {
                    createSparkles(box);
                }, index * 300);
            });

            // Play success sound (if browser supports)
            playSuccessSound();

            // Clean up after animation
            setTimeout(() => {
                if (overlay.parentNode) {
                    overlay.parentNode.removeChild(overlay);
                }
                if (message.parentNode) {
                    message.parentNode.removeChild(message);
                }
            }, 5000);
        }

        function createConfetti(container) {
            const confetti = document.createElement('div');
            confetti.className = 'confetti';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.animationDelay = Math.random() * 3 + 's';
            confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
            container.appendChild(confetti);

            setTimeout(() => {
                if (confetti.parentNode) {
                    confetti.parentNode.removeChild(confetti);
                }
            }, 5000);
        }

        function createSparkles(element) {
            const rect = element.getBoundingClientRect();

            for (let i = 0; i < 8; i++) {
                const sparkle = document.createElement('div');
                sparkle.className = 'sparkle';
                sparkle.style.left = (rect.left + Math.random() * rect.width) + 'px';
                sparkle.style.top = (rect.top + Math.random() * rect.height) + 'px';
                sparkle.style.width = (Math.random() * 8 + 4) + 'px';
                sparkle.style.height = sparkle.style.width;
                sparkle.style.animationDelay = Math.random() * 1 + 's';
                sparkle.style.position = 'fixed';
                sparkle.style.zIndex = '9998';

                document.body.appendChild(sparkle);

                setTimeout(() => {
                    if (sparkle.parentNode) {
                        sparkle.parentNode.removeChild(sparkle);
                    }
                }, 2000);
            }
        }

        function playSuccessSound() {
            try {
                // Create audio context for success sound
                const audioContext = new(window.AudioContext || window.webkitAudioContext)();

                // Create a simple success melody
                const frequencies = [523.25, 659.25, 783.99, 1046.50]; // C, E, G, C (octave)

                frequencies.forEach((freq, index) => {
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    oscillator.frequency.setValueAtTime(freq, audioContext.currentTime + index * 0.2);
                    gainNode.gain.setValueAtTime(0.1, audioContext.currentTime + index * 0.2);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + index * 0.2 + 0.3);

                    oscillator.start(audioContext.currentTime + index * 0.2);
                    oscillator.stop(audioContext.currentTime + index * 0.2 + 0.3);
                });
            } catch (e) {
                // Audio context not supported, skip sound
                console.log('Audio not supported');
            }
        }

        function previewDocument() {
            // Create a new window for preview
            const previewWindow = window.open('', '_blank', 'width=800,height=600');
            const documentHtml = document.querySelector('.document-container').outerHTML;

            previewWindow.document.open();
            previewWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Preview - Berita Acara Akreditasi</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            padding: 20px;
                            margin: 0;
                            background: #fff3e0;
                        }
                        .document-container {
                            background: white;
                            padding: 30px;
                            max-width: 800px;
                            margin: 0 auto;
                            box-shadow: 0 0 10px rgba(0,0,0,0.1);
                        }
                        .signature-box {
                            border: 1px solid #f7941d;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        .signature-placeholder {
                            color: #b98a55;
                            font-style: italic;
                        }
                        .signature-box img {
                            max-width: 100%;
                            max-height: 100%;
                            object-fit: contain;
                        }
                        .button-container {
                            text-align: center;
                            margin-top: 30px;
                            background: #fff3e0;
                            padding: 20px;
                        }
                        .btn-preview {
                            padding: 10px 20px;
                            border: none;
                            border-radius: 5px;
                            cursor: pointer;
                            margin: 0 5px;
                            font-weight: bold;
                        }
                        .btn-print { background: #ef6c00; color: white; }
                        .btn-close { background: #b98a55; color: white; }
                        @media print {
                            body { margin: 0; padding: 0; background: white; }
                            .button-container { display: none; }
                        }
                    </style>
                </head>
                <body>
                    ${documentHtml}
                    <div class="button-container">
                        <button onclick="window.print()" class="btn-preview btn-print">Print</button>
                        <button onclick="window.close()" class="btn-preview btn-close">Tutup</button>
                    </div>
                </body>
                </html>
            `);
            previewWindow.document.close();
        }

        // Helper function to convert image URL to base64
        async function urlToBase64(url) {
            try {
                const response = await fetch(url);
                const blob = await response.blob();
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = () => resolve(reader.result);
                    reader.onerror = reject;
                    reader.readAsDataURL(blob);
                });
            } catch (error) {
                console.error('Error converting URL to base64:', error);
                return null;
            }
        }

        function closeEttdModal() {
            const modal = document.getElementById('ettdModal');
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
        }

        function showEttdModal(title, body, footer) {
            document.getElementById('ettdModalTitle').textContent = title;
            document.getElementById('ettdModalBody').innerHTML = body;
            document.getElementById('ettdModalFooter').innerHTML = footer || '';
            const modal = document.getElementById('ettdModal');
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
        }

        function showShareModal(shareUrl) {
            const body = `<input id="ettdShareUrl" class="ettd-share-input" type="text" value="${shareUrl}" readonly>`;
            const footer = `<button type="button" class="btn btn-secondary" onclick="closeEttdModal()">Tutup</button><button type="button" class="btn btn-primary" onclick="copyShareLink()">Copy Link</button>`;
            showEttdModal('Bagikan Link E-TTD', body, footer);
        }

        async function copyShareLink() {
            const input = document.getElementById('ettdShareUrl');
            try {
                await navigator.clipboard.writeText(input.value);
            } catch (error) {
                input.select();
                document.execCommand('copy');
            }
            showEttdModal('Link Berhasil Disalin', `<p class="mb-0">Link E-TTD sudah disalin ke clipboard.</p><input class="ettd-share-input mt-3" type="text" value="${input.value}" readonly>`, `<button type="button" class="btn btn-primary" onclick="closeEttdModal()">Tutup</button>`);
        }

        // Share document link
        function shareDocument() {
            if (!ttdToken) {
                showEttdModal('Link Tidak Tersedia', '<p class="mb-0">Token E-TTD tidak ditemukan.</p>', '<button type="button" class="btn btn-primary" onclick="closeEttdModal()">Tutup</button>');
                return;
            }
            const shareUrl = `${window.location.origin}/ttd/${ttdToken}`;
            showShareModal(shareUrl);
        }

        // Legacy fallback retained for compatibility; primary share modal is showEttdModal.
        function showShareModalLegacy(shareUrl) {
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(74, 40, 10, 0.55);
                z-index: 10000;
                display: flex;
                align-items: center;
                justify-content: center;
            `;

            modal.innerHTML = `
                <div style="
                    background: white;
                    padding: 30px;
                    border-radius: 15px;
                    max-width: 500px;
                    width: 90%;
                    text-align: center;
                    position: relative;
                    border-top: 5px solid #f7941d;
                ">
                    <h3 style="margin-bottom: 20px; color: #b34700;">Share Document</h3>
                    <p style="margin-bottom: 15px; color: #8a5a2b;">Copy link di bawah ini untuk membagikan dokumen:</p>
                    <input type="text" value="${shareUrl}" readonly style="
                        width: 100%;
                        padding: 12px;
                        border: 2px solid #f7941d;
                        border-radius: 8px;
                        font-size: 14px;
                        margin-bottom: 20px;
                        text-align: center;
                    " onclick="this.select()">
                    <div>
                        <button onclick="copyToClipboardFallback('${shareUrl}')" style="
                            background: #ef6c00;
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 20px;
                            margin-right: 10px;
                            cursor: pointer;
                        ">Copy</button>
                        <button onclick="document.body.removeChild(this.closest('div').parentElement)" style="
                            background: #b98a55;
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 20px;
                            cursor: pointer;
                        ">Close</button>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            // Close modal when clicking outside
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    document.body.removeChild(modal);
                }
            });
        }

        // Fallback copy function
        function copyToClipboardFallback(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
                alert('Link berhasil disalin!');
            } catch (err) {
                alert('Gagal menyalin link. Silakan copy manual.');
            }

            document.body.removeChild(textArea);
        }

        async function downloadDocument() {
            // Create PDF
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            // Add title
            doc.setFontSize(16);
            doc.setFont(undefined, 'bold');
            doc.text('BERITA ACARA', 105, 20, {
                align: 'center'
            });
            doc.text('VISITASI TIM ASESOR AKREDITASI', 105, 30, {
                align: 'center'
            });
            doc.text('BALAI PELATIHAN KESEHATAN CIKARANG', 105, 40, {
                align: 'center'
            });

            // Add content
            doc.setFontSize(12);
            doc.setFont(undefined, 'normal');

            const content = [
                'Pada hari ini, Selasa Tanggal 24 Juni 2025, Pukul 14.45 Waktu Indonesia Barat,',
                'telah diselenggarakan visitasi dalam rangka verifikasi data Akreditasi Lembaga',
                'Penyelenggara Pelatihan di Bidang Sistem Teknologi Berbasis Komputer di',
                'Balai Pelatihan Kesehatan Cikarang oleh:',
                '',
                '1. Nama: Darusman',
                '   Jabatan: Ketua Tim Asesor',
                '2. Nama: Utama Andri Arjita',
                '   Jabatan: Anggota Tim Asesor',
                '3. Nama: Sari Novianti',
                '   Jabatan: Anggota Tim Asesor',
                '',
                'Berdasarkan hasil visitasi dan verifikasi terhadap bahan/dokumen, Tim Asesor',
                'memberikan hasil rekomendasi untuk memenuhi persyaratan Akreditasi',
                'sebagaimana catatan terlampir.',
                '',
                'Demikian, Berita Acara ini dibuat untuk dapat diketahui bersama:'
            ];

            let yPosition = 60;
            content.forEach(line => {
                doc.text(line, 20, yPosition);
                yPosition += 7;
            });

            // Add signature sections
            yPosition += 20;

            // Left signature
            doc.text('Ketua Tim Asesor', 40, yPosition);
            if (signatures.asesor1 && signatures.asesor1.data) {
                const base64Data = await urlToBase64(signatures.asesor1.data);
                if (base64Data) {
                    doc.addImage(base64Data, 'PNG', 20, yPosition + 5, 60, 30);
                }
            }
            doc.text('Darusman', 40, yPosition + 40);
            doc.text('Jakarta, 24 Juni 2026', 40, yPosition + 50);

            // Right signature
            doc.text('{{ $namaLembaga }}', 120, yPosition);
            if (signatures.kepala && signatures.kepala.data) {
                const base64Data = await urlToBase64(signatures.kepala.data);
                if (base64Data) {
                    doc.addImage(base64Data, 'PNG', 120, yPosition + 12, 60, 30);
                }
            }
            doc.text('{{ $namaPimpinan }}', 120, yPosition + 47);

            // Members signatures
            yPosition += 70;
            doc.text('Anggota:', 20, yPosition);
            yPosition += 10;

            doc.text('1. Utama Andri Arjita', 20, yPosition);
            if (signatures.asesor2 && signatures.asesor2.data) {
                const base64Data = await urlToBase64(signatures.asesor2.data);
                if (base64Data) {
                    doc.addImage(base64Data, 'PNG', 150, yPosition - 5, 40, 20);
                }
            }
            yPosition += 25;

            doc.text('2. Sari Novianti', 20, yPosition);
            if (signatures.asesor3 && signatures.asesor3.data) {
                const base64Data = await urlToBase64(signatures.asesor3.data);
                if (base64Data) {
                    doc.addImage(base64Data, 'PNG', 150, yPosition - 5, 40, 20);
                }
            }

            // Save the PDF
            const timestamp = new Date().toISOString().replace(/[:.]/g, '-').split('T')[0];
            const fileName = `Berita_Acara_Akreditasi_PSTK_${date('Y')}_${timestamp}.pdf`;
            doc.save(fileName);
        }

        // Real-time polling for signature updates
        let pollingInterval;
        let lastSignatureCount = 0;

        function startPolling() {
            // Poll every 5 seconds for real-time updates
            pollingInterval = setInterval(() => {
                fetchSignatureUpdates();
            }, 5000);
        }

        function stopPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }

        async function fetchSignatureUpdates(isInitialLoad = false) {
            try {
                if (!ttdToken) return;

                const response = await fetch(`/api/ttd/${ttdToken}/signatures`);
                const result = await response.json();

                if (response.ok && result.signatures) {
                    const currentSignatureCount = Object.values(result.signatures)
                        .filter(signature => signature.signed).length;

                    // On initial load, load all existing signatures
                    // On subsequent calls, only process new signatures
                    if (isInitialLoad || currentSignatureCount > lastSignatureCount) {
                        // Update signatures object with data
                        Object.entries(result.signatures).forEach(([signerType, signature]) => {
                            if (!signature.signed) return;
                            const sig = {
                                jenis_user: signerType,
                                nama_user: signature.name,
                                jabatan_user: signature.title,
                                tgl_waktu_surat: signature.signed_at,
                                ttd: signature.signature_url
                            };
                            if (!signatures[sig.jenis_user]) {
                                signatures[sig.jenis_user] = {
                                    name: sig.nama_user,
                                    title: sig.jabatan_user,
                                    signed_at: sig.tgl_waktu_surat,
                                    data: sig.ttd // This is now a URL, not base64
                                };

                                // Update the signature box visually
                                updateSignatureBoxFromServer(sig);

                                // Show notification for new signature (only if not initial load)
                                if (!isInitialLoad) {
                                    showNewSignatureNotification(sig);
                                }
                            }
                        });

                        lastSignatureCount = currentSignatureCount;
                        updateSignatureCount();
                    }

                    // Update document status if fully signed and still editable
                    if (!result.ba_submitted && result.is_fully_signed && Object.keys(signatures).length === 4) {
                        const statusElement = document.getElementById('statusText');
                        const statusContainer = document.getElementById('status');
                        const submitControls = document.getElementById('submitControls');

                        statusElement.textContent = 'Semua tanda tangan telah lengkap! Silakan submit dokumen.';
                        statusContainer.className = 'status-info';
                        statusContainer.style.background = '#fff0e0';
                        statusContainer.style.border = '1px solid #f7941d';
                        statusContainer.style.color = '#b34700';
                        submitControls.style.display = 'block';
                    } else if (result.ba_submitted) {
                        document.getElementById('beritaAcaraStatus').value = 'submitted';
                        updateSignatureCount();
                    }
                }
            } catch (error) {
                console.error('Error fetching signature updates:', error);
            }
        }

        function updateSignatureBoxFromServer(signature) {
            const signatureBox = document.querySelector(`[onclick="openSignatureModal('${signature.jenis_user}')"]`);
            if (signatureBox && signature.ttd) {
                signatureBox.innerHTML =
                    `<img src="${signature.ttd}" style="width: 100%; height: 100%; object-fit: contain;">`;
                signatureBox.classList.add('signed');
                signatureBox.onclick = null; // Disable clicking on signed boxes
                signatureBox.style.cursor = 'default';
            }
        }

        function showNewSignatureNotification(signature) {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #ef6c00 0%, #f7941d 100%);
                color: white;
                padding: 15px 20px;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.25);
                z-index: 10000;
                font-family: 'Segoe UI', sans-serif;
                font-size: 14px;
                max-width: 300px;
                animation: slideInRight 0.5s ease-out;
            `;

            notification.innerHTML = `
                <div style="font-weight: bold;">Tanda Tangan Baru</div>
                <div style="font-size: 12px; opacity: 0.9;">${signature.nama_user} telah menandatangani dokumen</div>
            `;

            // Add animation keyframes if not already added
            if (!document.querySelector('#notification-styles')) {
                const style = document.createElement('style');
                style.id = 'notification-styles';
                style.textContent = `
                    @keyframes slideInRight {
                        from { transform: translateX(100%); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                    @keyframes slideOutRight {
                        from { transform: translateX(0); opacity: 1; }
                        to { transform: translateX(100%); opacity: 0; }
                    }
                `;
                document.head.appendChild(style);
            }

            document.body.appendChild(notification);

            // Auto remove notification after 5 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.5s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 500);
            }, 5000);
        }

        // Initialize on page load
        window.addEventListener('load', () => {
            updateSignatureCount();

            // Load existing signatures from server
             if (ttdToken) {
                fetchSignatureUpdates(true).then(() => {
                    startPolling(); // Start real-time polling after initial load
                });
            }
        });

        // Stop polling when page is hidden/closed
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopPolling();
            } else {
                startPolling();
            }
        });

        // Stop polling before page unload
        window.addEventListener('beforeunload', () => {
            stopPolling();
        });

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('signatureModal');
            if (event.target === modal) {
                closeSignatureModal();
            }
        }
    </script>
</body>

</html>
