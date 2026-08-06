<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tanda Tangan Berita Acara Akreditasi PSTK 2025</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(45deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .main-content {
            padding: 30px;
        }

        .document-container {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            position: relative;
        }

        .share-button {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 0.9em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
        }

        .share-button:hover {
            background: linear-gradient(45deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
        }

        .share-button:active {
            transform: translateY(0);
        }

        .document-title {
            text-align: center;
            font-size: 1.8em;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .document-content {
            line-height: 1.8;
            color: #34495e;
            margin-bottom: 30px;
        }

        .signature-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }

        .signature-left {
            text-align: center;
        }

        .signature-right {
            text-align: center;
        }

        .signature-box {
            width: 200px;
            height: 100px;
            border: 2px solid #3498db;
            border-radius: 8px;
            margin: 20px auto;
            position: relative;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .signature-box:hover {
            border-color: #2980b9;
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }

        .signature-box.signed {
            border-color: #27ae60;
            background: #f8fff8;
        }

        .signature-placeholder {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #7f8c8d;
            font-size: 0.9em;
            pointer-events: none;
        }

        .signature-canvas {
            width: 100%;
            height: 100%;
            border-radius: 6px;
        }

        .signature-name {
            margin-top: 10px;
            font-weight: bold;
            color: #2c3e50;
        }

        .signature-title {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-top: 5px;
        }

        .members-section {
            margin-top: 40px;
        }

        .members-title {
            font-weight: bold;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .member-signature {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            gap: 20px;
        }

        .member-info {
            text-align: center;
            min-width: 200px;
        }

        .member-signature-box {
            width: 150px;
            height: 80px;
            border: 2px solid #3498db;
            border-radius: 8px;
            position: relative;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .member-signature-box:hover {
            border-color: #2980b9;
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
        }

        .member-signature-box.signed {
            border-color: #27ae60;
            background: #f8fff8;
        }

        .controls {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, #2980b9, #3498db);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #95a5a6, #7f8c8d);
            color: white;
        }

        .btn-secondary:hover {
            background: linear-gradient(45deg, #7f8c8d, #95a5a6);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(127, 140, 141, 0.4);
        }

        .btn-success {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            position: relative;
        }

        .modal-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .canvas-container {
            border: 2px solid #3498db;
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
            color: #2c3e50;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .signature-form {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .signature-form h3 {
            margin-bottom: 15px;
            color: #2c3e50;
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
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }

        .close:hover {
            color: #000;
        }

        .status-info {
            background: #e8f5e8;
            border: 1px solid #27ae60;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .status-incomplete {
            background: #fff3cd;
            border: 1px solid #ffc107;
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
            width: 10px;
            height: 10px;
            background: #ff6b6b;
            animation: fall 3s linear infinite;
        }

        .confetti:nth-child(2n) {
            background: #4ecdc4;
        }

        .confetti:nth-child(3n) {
            background: #45b7d1;
        }

        .confetti:nth-child(4n) {
            background: #f9ca24;
        }

        .confetti:nth-child(5n) {
            background: #6c5ce7;
        }

        .confetti:nth-child(6n) {
            background: #a29bfe;
        }

        .confetti:nth-child(7n) {
            background: #fd79a8;
        }

        .confetti:nth-child(8n) {
            background: #00b894;
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

        .fireworks {
            position: absolute;
            border-radius: 50%;
            animation: firework 1s ease-out infinite;
        }

        @keyframes firework {
            0% {
                transform: scale(0);
                opacity: 1;
            }

            100% {
                transform: scale(1);
                opacity: 0;
            }
        }

        .celebration-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4, #45b7d1, #f9ca24);
            background-size: 400% 400%;
            animation: rainbow 2s ease infinite;
            color: white;
            padding: 30px 50px;
            border-radius: 20px;
            font-size: 2em;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            z-index: 10000;
            opacity: 0;
            animation: celebration-popup 4s ease-in-out;
        }

        @keyframes rainbow {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes celebration-popup {
            0% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.5);
            }

            20% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.1);
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
            background: #ffd700;
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
            background: linear-gradient(45deg, #00b894, #00cec9);
            border: 2px solid #00b894;
            color: white;
            animation: pulse-success 2s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        @keyframes pulse-success {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(0, 184, 148, 0.3);
            }

            50% {
                box-shadow: 0 0 30px rgba(0, 184, 148, 0.6);
            }
        }

        .status-complete::before {
            content: '🚁';
            position: absolute;
            right: -30px;
            animation: slide-emoji 3s linear infinite;
        }

        @keyframes slide-emoji {
            0% {
                right: -30px;
            }

            100% {
                right: 100%;
            }
        }

        @media (max-width: 768px) {
            .signature-section {
                grid-template-columns: 1fr;
                gap: 20px;
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
            <h1>📋 Penandatanganan Digital</h1>
            <p>Berita Acara Akreditasi PSTK 2025</p>
        </div>

        <div class="main-content">
            <div class="document-container">
                <button class="share-button" onclick="shareDocument()">
                    🔗 Share Link
                </button>
                <div class="document-title">
                    BERITA ACARA<br>
                    VISITASI TIM ASESOR AKREDITASI<br>
                    {{ strtoupper($namaLembaga ?? 'LEMBAGA BELUM DITENTUKAN') }}
                </div>

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

                    <p>Berdasarkan hasil visitasi dan verifikasi terhadap bahan/dokumen, Tim Asesor memberikan hasil
                        rekomendasi untuk memenuhi persyaratan Akreditasi sebagaimana catatan terlampir.</p>

                    <p><strong>Demikian, Berita Acara ini dibuat untuk dapat diketahui bersama:</strong></p>
                </div>

                <div class="signature-section">
                    <div class="signature-left">
                        <div class="signature-name">Ketua Tim Asesor</div>
                        <div class="signature-box" onclick="openSignatureModal('asesor1')">
                            <div class="signature-placeholder">Klik untuk tanda tangan</div>
                        </div>
                        <div class="signature-name">{{ $asesorData['asesor1']['name'] ?? 'Belum ditentukan' }}</div>
                        <div style="margin-top: 20px; color: #7f8c8d;">{{ $signatureDate ?? 'Jakarta, 24 Juni 2025' }}
                        </div>
                    </div>

                    <div class="signature-right">
                        <div class="signature-name">
                            {{ $leaderData['title'] ?? 'Kepala ' . ($namaLembaga ?? 'Lembaga Belum Ditentukan') }}
                        </div>
                        <div class="signature-box" onclick="openSignatureModal('kepala')">
                            <div class="signature-placeholder">Klik untuk tanda tangan</div>
                        </div>
                        <div class="signature-name">{{ $leaderData['name'] ?? 'Belum ditentukan' }}</div>
                    </div>
                </div>

                <div class="members-section">
                    <div class="members-title">Anggota:</div>

                    <div class="member-signature">
                        <div class="member-info">
                            <strong>1. {{ $asesorData['asesor2']['name'] ?? 'Belum ditentukan' }}</strong>
                        </div>
                        <div class="member-signature-box" onclick="openSignatureModal('asesor2')">
                            <div class="signature-placeholder">Klik untuk tanda tangan</div>
                        </div>
                    </div>

                    <div class="member-signature">
                        <div class="member-info">
                            <strong>2. {{ $asesorData['asesor3']['name'] ?? 'Belum ditentukan' }}</strong>
                        </div>
                        <div class="member-signature-box" onclick="openSignatureModal('asesor3')">
                            <div class="signature-placeholder">Klik untuk tanda tangan</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="status" class="status-info status-incomplete">
                <strong>Status:</strong> <span id="statusText">Menunggu tanda tangan (0/4 selesai)</span>
            </div>

            <div class="controls" id="submitControls" style="display: none; text-align: center;">
                <button class="btn btn-success" id="submitBaBtn" onclick="submitDocument()">📄 SUBMIT BERITA ACARA</button>
                <button class="btn btn-danger" id="resetBaBtn" onclick="resetBeritaAcara()" style="display:none;">♻️ RESET BERITA ACARA</button>
                {{-- <button class="btn btn-primary" onclick="downloadDocument()" style="margin-left: 10px;">📄 DOWNLOAD PDF</button> --}}
            </div>

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
                <h3>📝 Data Penandatangan</h3>
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
                    <input type="hidden" id="pengajuanId" name="pengajuan_id" value="{{ $pengajuan->id ?? '' }}">
                    <input type="hidden" id="beritaAcaraStatus" value="{{ $pengajuan->berita_acara ?? '' }}">
                </form>
            </div>

            <!-- Canvas Tanda Tangan -->
            <div class="modal-header">
                <h3>✍️ Area Tanda Tangan</h3>
                <p>Gambar tanda tangan Anda di area di bawah ini</p>
            </div>
            <div class="canvas-container">
                <canvas id="signatureCanvas" class="signature-canvas-modal" width="540" height="200"></canvas>
            </div>

            <div class="modal-controls">
                <button class="btn btn-secondary" onclick="clearCanvas()">🗑️ Hapus</button>
                <button class="btn btn-primary" onclick="saveSignature()">✅ Simpan</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
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
            ctx.strokeStyle = '#2c3e50';

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
                modalHeader.style.color = '#e67e22';
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
            const pengajuanId = document.getElementById('pengajuanId').value;

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
            saveBtn.innerHTML = '⏳ Menyimpan...';
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
                        pengajuan_id: {{ $pengajuan->id ?? 0 }},
                        signer_type: signerType,
                        signer_name: signerName,
                        signer_title: signerTitle,
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

                        // Add a small celebration for individual signature
                        const miniCelebration = document.createElement('div');
                        miniCelebration.innerHTML = '✅';
                        miniCelebration.style.position = 'absolute';
                        miniCelebration.style.top = '-10px';
                        miniCelebration.style.right = '-10px';
                        miniCelebration.style.fontSize = '24px';
                        miniCelebration.style.animation = 'celebration-popup 1s ease-out';
                        miniCelebration.style.zIndex = '1000';
                        signatureBox.style.position = 'relative';
                        signatureBox.appendChild(miniCelebration);

                        setTimeout(() => {
                            if (miniCelebration.parentNode) {
                                miniCelebration.parentNode.removeChild(miniCelebration);
                            }
                        }, 1000);

                    }, 150);

                    // Remove success message alert
                    // const isReplacement = signatures[currentSignatureTarget] && signatures[currentSignatureTarget].data !== signatureData;
                    // const message = isReplacement ?
                    //     `Tanda tangan berhasil diganti!\nNama: ${signerName}\nJabatan: ${signerTitle}` :
                    //     `Tanda tangan berhasil disimpan!\nNama: ${signerName}\nJabatan: ${signerTitle}`;
                    // alert(message);

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

            if (beritaAcaraStatus && beritaAcaraStatus !== '-') {
                if (submitBaBtn) submitBaBtn.style.display = 'none';
                if (resetBaBtn) resetBaBtn.style.display = 'inline-block';
                statusElement.textContent = '✅ Berita Acara telah disubmit. Semua tanda tangan lengkap.';
                submitControls.style.display = 'flex';
                return;
            }

            statusElement.textContent = `Tanda tangan tersimpan (${signedCount}/4 selesai)`;

            if (signedCount > 0) {
                statusContainer.className = 'status-info';
                if (signedCount === 4) {
                    statusElement.textContent = '✅ Semua tanda tangan telah lengkap! Silakan submit dokumen.';
                    statusContainer.className = 'status-info';
                    statusContainer.style.background = '#d4edda';
                    statusContainer.style.border = '1px solid #c3e6cb';
                    statusContainer.style.color = '#155724';

                    // Show submit button
                    submitControls.style.display = 'flex';

                    // Add pulse animation to submit button
                    setTimeout(() => {
                        const submitBtn = submitControls.querySelector('.btn-success');
                        submitBtn.style.animation = 'pulse-success 1.5s ease-in-out infinite';
                    }, 100);
                }
            } else {
                statusContainer.className = 'status-info status-incomplete';
                submitControls.style.display = 'none';
            }
        }

        function submitDocument() {
            const signedCount = Object.keys(signatures).length;

            if (signedCount !== 4) {
                alert('Semua tanda tangan harus lengkap sebelum submit!');
                return;
            }

            console.log('Document submitted, triggering celebration...'); // Debug log

            // Update status to submitted
            const statusElement = document.getElementById('statusText');
            const statusContainer = document.getElementById('status');
            const submitControls = document.getElementById('submitControls');

            statusElement.textContent = '🎉 DOKUMEN BERHASI DITANDATANGANI! 🎉';
            statusContainer.className = 'status-info status-complete';

            // Hide submit button
            submitControls.style.display = 'none';

            // Force trigger celebration with slight delay
            setTimeout(() => {
                console.log('About to trigger celebration...'); // Debug log
                triggerCelebration();
            }, 500);

            // Disable all signature boxes to prevent further editing
            const allSignatureBoxes = document.querySelectorAll('.signature-box, .member-signature-box');
            allSignatureBoxes.forEach(box => {
                box.style.pointerEvents = 'none';
                box.style.opacity = '0.8';
                box.onclick = null;
            });

            // No automatic redirect - user stays on current page after celebration
        }

        function triggerCelebration() {
            console.log('Celebration triggered!'); // Debug log

            // Create celebration overlay
            const overlay = document.createElement('div');
            overlay.className = 'celebration-overlay';
            document.body.appendChild(overlay);

            // Create celebration message
            const message = document.createElement('div');
            message.className = 'celebration-message';
            message.innerHTML = `
                🎊 SELAMAT! 🎊<br>
                <span style="font-size: 0.7em;">Berita Acara Berhasil Ditandatangani!</span><br>
                <span style="font-size: 0.5em;">🎉 Akreditasi PSTK 2025 Selesai! 🎉</span>
            `;
            message.style.display = 'block';
            message.style.opacity = '1';
            document.body.appendChild(message);

            // Create confetti
            for (let i = 0; i < 100; i++) {
                setTimeout(() => {
                    createConfetti(overlay);
                }, i * 50);
            }

            // Create fireworks
            for (let i = 0; i < 8; i++) {
                setTimeout(() => {
                    createFirework(overlay);
                }, i * 500);
            }

            // Create sparkles around signature boxes
            const signatureBoxes = document.querySelectorAll('.signature-box.signed, .member-signature-box.signed');
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

        function createFirework(container) {
            const firework = document.createElement('div');
            firework.className = 'fireworks';
            firework.style.left = Math.random() * 100 + '%';
            firework.style.top = Math.random() * 50 + '%';
            firework.style.width = (Math.random() * 100 + 50) + 'px';
            firework.style.height = firework.style.width;

            const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#f9ca24', '#6c5ce7', '#a29bfe'];
            firework.style.background =
                `radial-gradient(circle, ${colors[Math.floor(Math.random() * colors.length)]} 0%, transparent 70%)`;

            container.appendChild(firework);

            setTimeout(() => {
                if (firework.parentNode) {
                    firework.parentNode.removeChild(firework);
                }
            }, 1000);
        }

        function createSparkles(element) {
            const rect = element.getBoundingClientRect();

            for (let i = 0; i < 12; i++) {
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
                            background: #f5f5f5;
                        }
                        .document-container {
                            background: white;
                            padding: 30px;
                            max-width: 800px;
                            margin: 0 auto;
                            box-shadow: 0 0 10px rgba(0,0,0,0.1);
                        }
                        .signature-box, .member-signature-box {
                            border: 1px solid #ccc;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        .signature-placeholder {
                            color: #999;
                            font-style: italic;
                        }
                        .signature-box img, .member-signature-box img {
                            max-width: 100%;
                            max-height: 100%;
                            object-fit: contain;
                        }
                        .button-container {
                            text-align: center;
                            margin-top: 30px;
                            background: #f5f5f5;
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
                        .btn-print { background: #3498db; color: white; }
                        .btn-close { background: #95a5a6; color: white; }
                        @media print {
                            body { margin: 0; padding: 0; background: white; }
                            .button-container { display: none; }
                        }
                    </style>
                </head>
                <body>
                    ${documentHtml}
                    <div class="button-container">
                        <button onclick="window.print()" class="btn-preview btn-print">🖨️ Print</button>
                        <button onclick="window.close()" class="btn-preview btn-close">❌ Tutup</button>
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

        // Share document link
        function shareDocument() {
            const pengajuanId = document.getElementById('pengajuanId').value;
            if (!pengajuanId) {
                alert('ID Pengajuan tidak ditemukan!');
                return;
            }

            const currentDomain = window.location.origin;
            const shareUrl = `${currentDomain}/ttd/${pengajuanId}`;

            // Try to copy to clipboard
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(shareUrl).then(() => {
                    alert('Link berhasil disalin ke clipboard!\n\n' + shareUrl);
                }).catch(() => {
                    // Fallback if clipboard API fails
                    showShareModal(shareUrl);
                });
            } else {
                // Fallback for older browsers or non-secure contexts
                showShareModal(shareUrl);
            }
        }

        // Show share modal as fallback
        function showShareModal(shareUrl) {
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
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
                ">
                    <h3 style="margin-bottom: 20px; color: #2c3e50;">📤 Share Document</h3>
                    <p style="margin-bottom: 15px; color: #7f8c8d;">Copy link di bawah ini untuk membagikan dokumen:</p>
                    <input type="text" value="${shareUrl}" readonly style="
                        width: 100%;
                        padding: 12px;
                        border: 2px solid #3498db;
                        border-radius: 8px;
                        font-size: 14px;
                        margin-bottom: 20px;
                        text-align: center;
                    " onclick="this.select()">
                    <div>
                        <button onclick="copyToClipboardFallback('${shareUrl}')" style="
                            background: linear-gradient(45deg, #3498db, #2980b9);
                            color: white;
                            border: none;
                            padding: 10px 20px;
                            border-radius: 20px;
                            margin-right: 10px;
                            cursor: pointer;
                        ">📋 Copy</button>
                        <button onclick="document.body.removeChild(this.closest('div').parentElement)" style="
                            background: linear-gradient(45deg, #95a5a6, #7f8c8d);
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
            doc.text('Jakarta, 24 Juni 2025', 40, yPosition + 50);

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
            const fileName = `Berita_Acara_Akreditasi_PSTK_2025_${timestamp}.pdf`;
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
                const pengajuanId = '{{ $pengajuan->id ?? '' }}';
                if (!pengajuanId) return;

                const response = await fetch(`/api/signatures?pengajuan_id=${pengajuanId}`);
                const result = await response.json();

                if (result.status === 'success') {
                    const currentSignatureCount = result.data.signature_count;

                    // On initial load, load all existing signatures
                    // On subsequent calls, only process new signatures
                    if (isInitialLoad || currentSignatureCount > lastSignatureCount) {
                        // Update signatures object with data
                        result.data.signatures.forEach(sig => {
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

                    // Update document status if fully signed
                    if (result.data.is_fully_signed && Object.keys(signatures).length === 4) {
                        const statusElement = document.getElementById('statusText');
                        const statusContainer = document.getElementById('status');
                        const submitControls = document.getElementById('submitControls');

                        statusElement.textContent = '✅ Semua tanda tangan telah lengkap! Silakan submit dokumen.';
                        statusContainer.className = 'status-info';
                        statusContainer.style.background = '#d4edda';
                        statusContainer.style.border = '1px solid #c3e6cb';
                        statusContainer.style.color = '#155724';
                        submitControls.style.display = 'block';
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
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px 20px;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                z-index: 10000;
                font-family: 'Segoe UI', sans-serif;
                font-size: 14px;
                max-width: 300px;
                animation: slideInRight 0.5s ease-out;
            `;

            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 20px;">🚁</span>
                    <div>
                        <div style="font-weight: bold;">Tanda Tangan Baru!</div>
                        <div style="font-size: 12px; opacity: 0.9;">${signature.nama_user} telah menandatangani dokumen</div>
                    </div>
                </div>
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
            const pengajuanId = '{{ $pengajuan->id ?? '' }}';
            if (pengajuanId) {
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
