<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tanda Tangan Berita Acara Sidang</title>
    <style>
        *{box-sizing:border-box} body{margin:0;padding:20px;font-family:Segoe UI,Tahoma,sans-serif;background:linear-gradient(135deg,#eef7ff,#dcecff);color:#26354a}.wrap{max-width:1050px;margin:auto;background:#fff;border-radius:14px;box-shadow:0 15px 35px rgba(32,78,130,.16);overflow:hidden;border-top:6px solid #1769aa}.header{background:linear-gradient(135deg,#155a91,#2686c5);color:#fff;padding:28px;text-align:center}.header h1{margin:0 0 8px;font-size:2rem}.header p{margin:0;opacity:.92}.content{padding:28px}.meta{background:#f5f9fd;border:1px solid #d7e6f4;border-radius:10px;padding:18px;margin-bottom:24px}.meta table{width:100%;border-collapse:collapse}.meta td{padding:5px 8px;vertical-align:top}.meta td:first-child{width:180px;font-weight:600;color:#315979}.signatures{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.signer{border:1px solid #d7e6f4;border-radius:10px;padding:18px;text-align:center;background:#fbfdff}.signer h3{margin:0 0 5px;color:#155a91;font-size:1.05rem}.signer p{min-height:42px;margin:4px 0;color:#5b6d7e}.sign-box{height:120px;border:2px dashed #2686c5;border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;background:#fff;margin:14px 0}.sign-box.signed{border-style:solid;border-color:#198754;background:#f1fff7}.sign-box img{max-width:90%;max-height:100px}.hint{color:#60778d;font-size:.85rem}.controls{text-align:center;margin-top:24px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap}.btn{border:0;border-radius:20px;padding:10px 18px;color:#fff;cursor:pointer;font-weight:600}.btn-primary{background:#1769aa}.btn-success{background:#198754}.btn-warning{background:#bd7600}.btn-secondary{background:#66727d}.status{text-align:center;margin-top:16px;font-weight:600}.modal{display:none;position:fixed;inset:0;background:rgba(20,43,68,.6);align-items:center;justify-content:center;padding:20px;z-index:10}.modal.show{display:flex}.card{width:min(100%,560px);background:#fff;border-radius:12px;padding:20px}.card h2{margin-top:0;color:#155a91}.canvas-wrap{border:1px solid #c5d9ea;border-radius:8px;overflow:hidden;background:#fff}.canvas-wrap canvas{display:block;width:100%;height:220px;touch-action:none}.modal-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:14px}
        @media(max-width:700px){.signatures{grid-template-columns:1fr}.content{padding:16px}.header h1{font-size:1.45rem}}
    </style>
</head>
<body>
<div class="wrap">
    <header class="header"><h1>Tanda Tangan Berita Acara Sidang</h1><p>Sidang Majelis Akreditasi</p></header>
    <main class="content">
        <section class="meta">
            <table>
                <tr><td>Lembaga</td><td>: {{ $pengajuan->profile->nama_lembaga ?? '-' }}</td></tr>
                <tr><td>Program</td><td>: {{ $pengajuan->id_jenis == 1 ? 'Sistem Teknologi Berbasis Komputer' : 'Statistik' }}</td></tr>
                <tr><td>Token Sidang</td><td>: {{ $pengajuan->ttd_sidang_token }}</td></tr>
            </table>
        </section>
        <section class="signatures">
            @foreach(['ketua_majelis' => 'Ketua Majelis', 'sekretaris_majelis' => 'Sekretaris Majelis', 'anggota_majelis' => 'Anggota Majelis'] as $type => $label)
                @php $signature = $signatures->get($type); $signed = $signature && $signature->status_ttd === 'signed' && $signature->ttd; @endphp
                <article class="signer">
                    <h3>{{ $label }}</h3>
                    <p>{{ $signature->nama_user ?? 'Belum diisi' }}<br><span class="hint">{{ $signature->jabatan_user ?? '-' }}</span></p>
                    <div class="sign-box {{ $signed ? 'signed' : '' }}" data-signer="{{ $type }}" onclick="openSignature('{{ $type }}')">
                        @if($signed)<img src="{{ asset($signature->ttd) }}" alt="Tanda tangan {{ $label }}">@else<span class="hint">Klik untuk tanda tangan</span>@endif
                    </div>
                    <div class="hint">{{ $signed ? 'Sudah ditandatangani' : 'Belum ditandatangani' }}</div>
                </article>
            @endforeach
        </section>
        <div id="status" class="status">Memuat status tanda tangan...</div>
        <div class="controls">
            @if(auth()->check() && auth()->user()->role == 2 && !$submitted)
                <button class="btn btn-success" id="submitBtn" onclick="submitBa()">SUBMIT BERITA ACARA SIDANG</button>
                <button class="btn btn-secondary" onclick="resetAll()">RESET TANDA TANGAN</button>
            @endif
            @if(auth()->check() && auth()->user()->role == 2 && $submitted)
                <button class="btn btn-warning" onclick="resetBa()">RESET BERITA ACARA SIDANG</button>
            @endif
            <a class="btn btn-primary" href="{{ route('ekspor.ba.sidang', $pengajuan->id) }}">GENERATE BA SIDANG</a>
            @if($submitted)<a class="btn btn-success" href="{{ route('ekspor.ba.sidang.ttd', $pengajuan->id) }}">GENERATE BA HASIL TTD</a>@endif
        </div>
    </main>
</div>
<div class="modal" id="signatureModal"><div class="card"><h2 id="modalTitle">Tanda Tangan</h2><div class="canvas-wrap"><canvas id="signatureCanvas" width="520" height="220"></canvas></div><div class="modal-actions"><button class="btn btn-secondary" onclick="closeSignature()">Batal</button><button class="btn btn-warning" onclick="clearCanvas()">Bersihkan</button><button class="btn btn-primary" onclick="saveSignature()">Simpan</button></div></div></div>
<script>
const token='{{ $pengajuan->ttd_sidang_token }}', csrf=document.querySelector('meta[name="csrf-token"]').content;
let activeSigner=null, drawing=false; const modal=document.getElementById('signatureModal'), canvas=document.getElementById('signatureCanvas'), ctx=canvas.getContext('2d');
ctx.lineWidth=2;ctx.lineCap='round';ctx.strokeStyle='#123b5d';
function point(e){const r=canvas.getBoundingClientRect(),x=(e.touches?e.touches[0].clientX:e.clientX)-r.left,y=(e.touches?e.touches[0].clientY:e.clientY)-r.top;return [x*canvas.width/r.width,y*canvas.height/r.height]}
canvas.addEventListener('pointerdown',e=>{drawing=true;ctx.beginPath();ctx.moveTo(...point(e))});canvas.addEventListener('pointermove',e=>{if(drawing){ctx.lineTo(...point(e));ctx.stroke()}});window.addEventListener('pointerup',()=>drawing=false);
function openSignature(type){if({{ $submitted?'true':'false' }})return;activeSigner=type;document.getElementById('modalTitle').textContent='Tanda Tangan '+type.replaceAll('_',' ');clearCanvas();modal.classList.add('show')}
function closeSignature(){modal.classList.remove('show');activeSigner=null} function clearCanvas(){ctx.clearRect(0,0,canvas.width,canvas.height)}
async function saveSignature(){if(!activeSigner)return;const response=await fetch('{{ route('ttd.sidang.save') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({token,signer_type:activeSigner,signature_data:canvas.toDataURL('image/png')})});const result=await response.json();if(!response.ok){alert(result.message||'Gagal menyimpan tanda tangan');return}closeSignature();location.reload()}
async function submitBa(){const response=await fetch('{{ route('ttd.sidang.submit.ba') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({token})});const result=await response.json();alert(result.message||'Selesai');if(response.ok)location.reload()}
async function resetAll(){if(!confirm('Reset seluruh tanda tangan sidang?'))return;await fetch('{{ route('ttd.sidang.reset.all') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({token})});location.reload()}
async function resetBa(){if(!confirm('Reset status Berita Acara Sidang?'))return;await fetch('{{ route('ttd.sidang.reset.ba') }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf},body:JSON.stringify({token})});location.reload()}
fetch('{{ route('ttd.sidang.signatures', ['token' => $pengajuan->ttd_sidang_token]) }}').then(r=>r.json()).then(d=>{document.getElementById('status').textContent=d.is_fully_signed?'Semua tanda tangan lengkap (3/3)':'Menunggu tanda tangan ('+Object.values(d.signatures).filter(x=>x.signed).length+'/3 selesai)'})
</script>
</body>
</html>
