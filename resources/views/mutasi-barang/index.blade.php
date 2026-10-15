@extends('layouts.app')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
    .filter-bar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px; background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; padding:14px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    .filter-bar select { padding:12px 14px; border:1px solid #E8EAF0; border-radius:14px; font-size:14px; background:#FBFBFD; color:var(--andon-ink); transition:border-color .18s ease,box-shadow .18s ease,background .18s ease; }
    .filter-bar select:focus { outline:none; border-color:var(--andon-ink); box-shadow:0 0 0 3px rgba(15,23,42,.06); background:#fff; }
    .card { background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); padding:20px; margin-bottom:20px; }
    .table-responsive { width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table { width:100%; min-width:920px; border-collapse:collapse; }
    th { background:#F8FAFC; padding:11px 14px; text-align:left; font-size:12px; font-weight:800; color:var(--andon-muted); letter-spacing:.04em; text-transform:uppercase; border-bottom:1px solid var(--andon-line); }
    td { padding:12px 14px; border-bottom:1px solid var(--andon-line); font-size:13px; color:var(--andon-ink); }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#F8FAFC; }
    .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; border:1px solid transparent; }
    .badge-green { background:var(--andon-green); color:#fff; }
    .badge-red { background:var(--andon-red); color:#fff; }
    .badge-blue { background:var(--andon-navy); color:#fff; }
    .alert-success { background:#F0FDF4; color:#14532D; border:1px solid #BBF7D0; padding:12px 16px; border-radius:12px; margin-bottom:16px; font-weight:600; font-size:13px; }
    .empty-state { text-align:center; padding:40px; color:var(--andon-faint); font-weight:600; }
    .page-actions{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
    .voice-mic-btn{width:44px;height:44px;border-radius:999px;display:grid;place-items:center;border:1px solid var(--andon-line-strong);background:#fff;cursor:pointer;flex:0 0 auto;transition:transform .14s,box-shadow .16s,background .16s,color .16s}
    .voice-mic-btn:hover{transform:translateY(-1px);box-shadow:var(--andon-shadow)}
    .voice-mic-btn--on{background:var(--andon-amber);border-color:var(--andon-amber-dim);box-shadow:0 0 0 4px var(--andon-amber-glow);animation:voice-pulse 1.1s infinite}
    @keyframes voice-pulse{0%,100%{box-shadow:0 0 0 4px var(--andon-amber-glow)}50%{box-shadow:0 0 0 7px rgba(239,131,84,.10)}}
    @media(prefers-reduced-motion:reduce){.voice-mic-btn--on{animation:none}}
    .voice-overlay{display:none;position:fixed;inset:0;z-index:50;background:rgba(15,23,42,.42);backdrop-filter:blur(2px);align-items:center;justify-content:center;padding:16px}
    .voice-overlay--open{display:flex}
    .voice-modal{width:min(560px,96vw);max-height:88vh;overflow:auto;background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 10px 28px rgba(15,23,42,.12),0 2px 6px rgba(15,23,42,.06);padding:18px;position:relative}
    .voice-modal::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
    .voice-modal-head{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:12px}
    .voice-modal-head h3{margin:0;font-size:15px;font-weight:800;letter-spacing:-.02em;color:var(--andon-ink)}
    .voice-transcript{font-size:12px;color:var(--andon-ink);background:#F8FAFC;border:1px solid var(--andon-line);border-radius:12px;padding:10px 12px;min-height:38px;word-break:break-word}
    .voice-preview{font-size:11px;color:var(--andon-muted);margin-top:8px;line-height:1.5}
    .voice-preview strong{color:var(--andon-ink)}
    .jenis-toggle{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .jenis-btn{padding:11px;border:1px solid #E8EAF0;border-radius:14px;text-align:center;cursor:pointer;font-weight:800;font-size:12px;background:#FBFBFD;color:var(--andon-muted);transition:all .18s}
    .jenis-btn.masuk.active{border-color:var(--andon-green);background:#F0FDF4;color:#14532D}
    .jenis-btn.keluar.active{border-color:var(--andon-red);background:#FEF2F2;color:#7F1D1D}
    @media (max-width:600px) {
        .page-header > div { width:100%; }
        .page-header > div:first-child { align-items:flex-start !important; gap:10px !important; }
        .page-actions .btn--primary{flex:1;justify-content:center}
        .filter-bar > * { flex:1 1 100%; width:100%; }
        .filter-bar .btn { justify-content:center; }
    }
</style>

<div class="page-header">
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="{{ request('from') === 'laporan' ? route('laporan.index') : (auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan'))) }}" class="back-link" aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
        <h1 class="andon-page-title" style="margin:0">Mutasi Barang</h1>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <div class="page-actions">
            <button type="button" class="voice-mic-btn" id="voice-index-btn" aria-label="Mutasi via suara" title="Ucapkan: masuk ATK-001 10 — hasil isi form, cek lalu simpan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" aria-hidden="true"><path d="M12 14a3 3 0 003-3V6a3 3 0 10-6 0v5a3 3 0 003 3z"/><path d="M19 10a7 7 0 01-14 0"/><path d="M12 19v3"/><path d="M8 22h8"/></svg>
            </button>
            <a href="{{ route('mutasi-barang.create') }}" class="btn btn--primary">+ Catat Mutasi</a>
        </div>
    @endif
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<form method="GET" action="{{ route('mutasi-barang.index') }}" class="filter-bar">
    <select name="jenis">
        <option value="">Semua Jenis</option>
        <option value="masuk" {{ request('jenis') === 'masuk' ? 'selected' : '' }}>Masuk</option>
        <option value="keluar" {{ request('jenis') === 'keluar' ? 'selected' : '' }}>Keluar</option>
    </select>
    <select name="barang_id">
        <option value="">Semua Barang</option>
        @foreach($barangs as $b)
            <option value="{{ $b->id }}" {{ request('barang_id') == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn--primary">Filter</button>
    @if(request()->hasAny(['jenis','barang_id']))
        <a href="{{ route('mutasi-barang.index') }}" class="btn btn--ghost">Reset</a>
    @endif
</form>

@if(auth()->user()->isAdmin() || auth()->user()->isStaff())
<div class="voice-overlay" id="voice-overlay" aria-hidden="true">
    <div class="voice-modal" role="dialog" aria-modal="true" aria-labelledby="voice-title">
        <div class="voice-modal-head">
            <h3 id="voice-title">Mutasi via suara</h3>
            <button type="button" class="btn btn--ghost" id="voice-close" style="min-height:36px">Tutup</button>
        </div>
        <div style="display:flex;gap:10px;align-items:center;margin-bottom:10px;flex-wrap:wrap">
            <button type="button" class="voice-mic-btn" id="voice-modal-btn" aria-label="Mulai rekam"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" aria-hidden="true"><path d="M12 14a3 3 0 003-3V6a3 3 0 10-6 0v5a3 3 0 003 3z"/><path d="M19 10a7 7 0 01-14 0"/><path d="M12 19v3"/><path d="M8 22h8"/></svg></button>
            <span style="font-size:11px;color:var(--andon-muted)">Ucapkan contoh: <em>"masuk ATK 001 sepuluh"</em> atau <em>"keluar kertas 5"</em></span>
            <button type="button" class="btn btn--ghost" id="voice-speak" style="min-height:36px;margin-left:auto">Bacakan</button>
        </div>
        <div class="voice-transcript" id="voice-transcript" aria-live="polite">Tekan mic lalu ucapkan</div>
        <div class="voice-preview" id="voice-preview"></div>
        <div class="voice-nosupport" id="voice-nosupport" style="display:none;margin-top:10px;font-size:11px;font-weight:700;color:#991B1B;background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:8px 10px">Browser ini belum dukung suara. Gunakan Chrome/Edge terbaru.</div>
        <form method="POST" action="{{ route('mutasi-barang.store') }}" style="margin-top:14px">
            @csrf
            <div style="display:flex;flex-direction:column;gap:12px">
                <label style="font-size:11px;font-weight:800;color:var(--andon-muted)">Barang</label>
                <select name="barang_id" id="voice-barang" required style="width:100%;padding:12px 14px;border:1px solid #E8EAF0;border-radius:14px;background:#FBFBFD">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $b)<option value="{{ $b->id }}" data-nama="{{ $b->nama }}">{{ $b->kode_barang }} — {{ $b->nama }}</option>@endforeach
                </select>
                <label style="font-size:11px;font-weight:800;color:var(--andon-muted)">Jenis</label>
                <div class="jenis-toggle">
                    <label class="jenis-btn masuk" data-jenis="masuk"><input type="radio" name="jenis" value="masuk" required style="display:none"> ↑ Masuk</label>
                    <label class="jenis-btn keluar" data-jenis="keluar"><input type="radio" name="jenis" value="keluar" required style="display:none"> ↓ Keluar</label>
                </div>
                <label style="font-size:11px;font-weight:800;color:var(--andon-muted)">Jumlah</label>
                <input type="number" id="voice-jumlah" name="jumlah" min="1" value="1" required style="width:100%;padding:12px 14px;border:1px solid #E8EAF0;border-radius:14px;background:#FBFBFD">
                <label style="font-size:11px;font-weight:800;color:var(--andon-muted)">Keterangan (opsional)</label>
                <textarea name="keterangan" rows="2" placeholder="Catatan..." style="width:100%;padding:12px 14px;border:1px solid #E8EAF0;border-radius:14px;background:#FBFBFD"></textarea>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                    <button type="submit" class="btn btn--primary" style="flex:1">Simpan Mutasi</button>
                    <a href="{{ route('mutasi-barang.create') }}" class="btn btn--ghost">Form lengkap</a>
                </div>
                <p style="font-size:11px;color:var(--andon-faint);margin:0">Hanya mengisi form — cek dulu baru Simpan. Validasi stok tetap di server.</p>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card">
    @if($mutasis->isEmpty())
        <div class="empty-state">Belum ada data mutasi.</div>
    @else
        <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th>Kategori</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Staff</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mutasis as $m)
                <tr>
                    <td style="color:var(--andon-muted); font-weight:600;">{{ $m->tanggal->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('barang.show', $m->barang) }}" style="color:var(--andon-navy);font-weight:800;">{{ $m->barang->nama }}</a>
                        <div style="font-size:11px;color:var(--andon-faint); font-weight:600;">{{ $m->barang->kode_barang }}</div>
                    </td>
                    <td><span class="badge badge-blue">{{ $m->barang->kategori->nama }}</span></td>
                    <td>
                        @if($m->jenis === 'masuk')
                            <span class="badge badge-green">↑ Masuk</span>
                        @else
                            <span class="badge badge-red">↓ Keluar</span>
                        @endif
                    </td>
                    <td><strong>{{ $m->jumlah }}</strong> {{ $m->barang->satuan }}</td>
                    <td style="color:var(--andon-muted);">{{ $m->keterangan ?? '-' }}</td>
                    <td style="font-weight:600;">{{ $m->staff->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </div>
        </table>
        <div style="margin-top:16px;">{{ $mutasis->links() }}</div>
    @endif
</div>
@if(auth()->user()->isAdmin() || auth()->user()->isStaff())
<script>
(function(){
    const overlay=document.getElementById('voice-overlay'), openBtn=document.getElementById('voice-index-btn'), modalBtn=document.getElementById('voice-modal-btn'), closeBtn=document.getElementById('voice-close');
    const tx=document.getElementById('voice-transcript'), pv=document.getElementById('voice-preview'), ns=document.getElementById('voice-nosupport'), speakBtn=document.getElementById('voice-speak');
    const sel=document.getElementById('voice-barang'), qty=document.getElementById('voice-jumlah');
    const transcribeUrl="{{ route('voice.transcribe') }}";
    const csrf=document.querySelector('meta[name="csrf-token"]')?.content || '';
    const SR=window.SpeechRecognition||window.webkitSpeechRecognition;
    const isIOS=/iPad|iPhone|iPod/.test(navigator.userAgent);
    const hasMediaRecorder=!!window.MediaRecorder && !!navigator.mediaDevices?.getUserMedia;
    const useWhisper=isIOS || !SR;
    function open(){ overlay.classList.add('voice-overlay--open'); overlay.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; }
    function close(){ overlay.classList.remove('voice-overlay--open'); overlay.setAttribute('aria-hidden','true'); document.body.style.overflow=''; try{ rec&&rec.stop(); }catch(e){} try{ if(mediaRecorder&&mediaRecorder.state==='recording') mediaRecorder.stop(); }catch(e){} if(streamRef){ streamRef.getTracks().forEach(t=>t.stop()); streamRef=null; } }
    openBtn?.addEventListener('click', open);
    closeBtn?.addEventListener('click', close); overlay?.addEventListener('click', e=>{ if(e.target===overlay) close(); });
    document.addEventListener('keydown', e=>{ if(e.key==='Escape' && overlay.classList.contains('voice-overlay--open')) close(); });
    const angka={satu:1,dua:2,tiga:3,empat:4,lima:5,enam:6,tujuh:7,delapan:8,sembilan:9,sepuluh:10,sebelas:11,'dua belas':12,'tiga belas':13,'empat belas':14,'lima belas':15,'enam belas':16,'tujuh belas':17,'delapan belas':18,'sembilan belas':19,'dua puluh':20,'tiga puluh':30,'empat puluh':40,'lima puluh':50,seratus:100,seribu:1000};
    function wordsToNum(s){ s=s.toLowerCase().trim(); if(/^\d+$/.test(s)) return parseInt(s,10); let t=s.replace(/-/g,' '); if(angka[t]!=null) return angka[t]; const m=t.match(/^(dua puluh|tiga puluh|empat puluh|lima puluh)\s*(satu|dua|tiga|empat|lima|enam|tujuh|delapan|sembilan)?$/); if(m) return (angka[m[1]]||0)+(angka[m[2]]||0); return null; }
    function norm(s){ return s.toLowerCase().replace(/[^a-z0-9\s]/g,' ').replace(/\s+/g,' ').trim(); }
    function parseMutasi(text){
        let raw=text.toLowerCase().replace(/\s+/g,' ').trim();
        const rawNorm=raw.replace(/\bke\s+luar\b/g,'keluar');
        let jenis=null, lastMasuk=-1, lastKeluar=-1;
        const reMasuk=/\b(masuk|tambah|nambah|ditambah|stok masuk)\b/g, reKeluar=/\b(keluar|keluarin|ambil|diambil|pakai|dipakai|kurang|dikurang)\b/g;
        let m; while((m=reMasuk.exec(rawNorm))!==null) lastMasuk=m.index;
        while((m=reKeluar.exec(rawNorm))!==null) lastKeluar=m.index;
        if(lastMasuk>=0||lastKeluar>=0) jenis = lastKeluar>lastMasuk ? 'keluar' : 'masuk';
        const numMatch=raw.match(/(\d+|satu|dua|tiga|empat|lima|enam|tujuh|delapan|sembilan|sepuluh|sebelas|dua belas|tiga belas|empat belas|lima belas|enam belas|tujuh belas|delapan belas|sembilan belas|dua puluh|tiga puluh|empat puluh|lima puluh|seratus|seribu)(?:\b|$)/);
        const jumlah=numMatch ? (wordsToNum(numMatch[1]) ?? parseInt(numMatch[1],10)) : null;
        let barangId=null, barangLabel=null, cand=[];
        const kodeHit=raw.match(/\b([a-z]{2,4})\s*[- ]?\s*(\d{1,4})\b/i);
        if(kodeHit){ const kode=(kodeHit[1]+'-'+kodeHit[2]).toUpperCase(); for(const o of sel.options){ if(o.value && o.textContent.toUpperCase().includes(kode)){ barangId=o.value; barangLabel=o.textContent.trim(); break; } } }
        if(!barangId){
            const rawTokens=norm(raw).split(' ').filter(Boolean);
            let bestScore=0, best=null;
            for(const o of sel.options){ if(!o.value) continue; const nm=(o.dataset.nama||o.textContent||'').toLowerCase(); const nmTokens=norm(nm).split(' ').filter(Boolean); if(!nmTokens.length) continue; let hit=0; for(const t of nmTokens) if(rawTokens.includes(t)) hit++; const score=hit/nmTokens.length; cand.push({o,score,label:o.textContent.trim()}); if(score>bestScore){ bestScore=score; best=o; } }
            cand.sort((a,b)=>b.score-a.score); if(best && bestScore>=0.5){ barangId=best.value; barangLabel=best.textContent.trim(); }
        }
        const _cand=cand.slice(0,3).filter(c=>c.score>0);
        return {jenis,jumlah,barangId,barangLabel,raw,_cand};
    }
    function setJenis(j){ document.querySelectorAll('.voice-modal .jenis-btn').forEach(el=>el.classList.remove('active')); const t=document.querySelector('.voice-modal .jenis-btn[data-jenis="'+j+'"]'); if(t){ t.classList.add('active'); t.querySelector('input').checked=true; } }
    function applyParsed(p, rawText){
        let applied=[];
        if(p.barangId){ sel.value=p.barangId; applied.push('Barang: <strong>'+p.barangLabel+'</strong>'); }
        if(p.jenis){ setJenis(p.jenis); applied.push('Jenis: <strong>'+p.jenis+'</strong>'); }
        if(p.jumlah && p.jumlah>=1){ qty.value=p.jumlah; applied.push('Jumlah: <strong>'+p.jumlah+'</strong>'); }
        if(applied.length) pv.innerHTML = applied.join(' · ') + '<br><span style="color:var(--andon-muted)">"'+rawText+'"</span> — cek lalu Simpan.';
        else { let sug=(p._cand||[]).map(c=>c.label).join(', '); pv.innerHTML='<span style="color:#991B1B">Tidak mengenali.</span> Coba: "masuk kertas HVS 10" atau "keluar pulpen sepuluh".' + (sug? '<br><span style="color:var(--andon-muted)">Mirip: '+sug+'</span>' : ''); }
    }
    function speak(text){ try{ const u=new SpeechSynthesisUtterance(text); u.lang='id-ID'; speechSynthesis.speak(u);}catch(e){} }
    function showNs(html){ ns.innerHTML=html; ns.style.display='block'; }
    function hideNs(){ ns.style.display='none'; }
    if(!window.isSecureContext) showNs('Butuh HTTPS — ngrok sudah OK.');
    speakBtn?.addEventListener('click', ()=>{ const p=parseMutasi(tx.textContent||''); let t='Belum ada input.'; if(p.barangLabel||p.jenis||p.jumlah) t=(p.jenis? p.jenis+' ':'')+(p.barangLabel? p.barangLabel+' ':'')+(p.jumlah? String(p.jumlah):'')+', cek lalu simpan.'; speak(t); });
    document.querySelectorAll('.voice-modal .jenis-btn').forEach(el=> el.addEventListener('click', ()=> setJenis(el.dataset.jenis)));
    let listening=false, rec=null, mediaRecorder=null, chunks=[], streamRef=null;
    if(SR && !useWhisper){
        rec=new SR(); rec.lang='id-ID'; rec.interimResults=true; rec.continuous=false; rec.maxAlternatives=1;
        rec.onstart=()=>{ listening=true; modalBtn.classList.add('voice-mic-btn--on'); openBtn.classList.add('voice-mic-btn--on'); tx.textContent='Mendengarkan...'; pv.textContent=''; hideNs(); };
        rec.onend=()=>{ listening=false; modalBtn.classList.remove('voice-mic-btn--on'); openBtn.classList.remove('voice-mic-btn--on'); if(tx.textContent==='Mendengarkan...') tx.textContent='Tekan mic lalu ucapkan'; };
        rec.onerror=(e)=>{ listening=false; modalBtn.classList.remove('voice-mic-btn--on'); openBtn.classList.remove('voice-mic-btn--on'); const map={ 'not-allowed':'Izin mic ditolak — tap gembok > Mikrofon > Izinkan.', 'no-speech':'Tak ada suara — coba dekat mic.', 'audio-capture':'Mic dipakai app lain.', 'network':'Butuh internet.' }; const code=e.error||''; if(map[code]) showNs(map[code]); else if(code) showNs('Gagal mic: '+code); };
        rec.onresult=(e)=>{ let final='', interim=''; for(let i=e.resultIndex;i<e.results.length;i++){ const r=e.results[i]; if(r.isFinal) final+=r[0].transcript+' '; else interim+=r[0].transcript; } const text=(final||interim).trim(); if(text) tx.textContent=text; if(e.results[e.results.length-1].isFinal && final.trim()){ const p=parseMutasi(final.trim()); applyParsed(p, final.trim()); speak((p.jenis||'')+' '+(p.barangLabel||'')+' '+(p.jumlah||'')+' — cek lalu simpan.'); } };
    }
    if(useWhisper && !hasMediaRecorder) showNs('Browser ini belum dukung rekam. Alternatif: tap kolom teks → ikon mic keyboard.');
    else if(useWhisper) tx.textContent='Tap mic di modal → ucap "masuk kertas HVS 10" → tap lagi untuk kirim (iPhone via Whisper).';
    async function startWhisper(){
        try{ hideNs(); const s=await navigator.mediaDevices.getUserMedia({audio:true}); streamRef=s; chunks=[]; let mime='audio/webm'; if(!MediaRecorder.isTypeSupported(mime)) mime='audio/mp4'; mediaRecorder=new MediaRecorder(s, mime?{mimeType:mime}:undefined); mediaRecorder.ondataavailable=e=>{ if(e.data.size) chunks.push(e.data); }; mediaRecorder.onstop=async()=>{
            listening=false; modalBtn.classList.remove('voice-mic-btn--on'); openBtn.classList.remove('voice-mic-btn--on'); tx.textContent='Menerjemahkan...';
            try{ const blob=new Blob(chunks,{type: mediaRecorder.mimeType||'audio/webm'}); const fd=new FormData(); fd.append('audio', blob, 'voice.webm'); const res=await fetch(transcribeUrl,{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'},body:fd}); const j=await res.json(); if(!res.ok) throw new Error(j.error||'Gagal transkripsi'); const text=(j.text||'').trim(); if(!text) throw new Error('Tidak terdengar — coba lagi.'); tx.textContent=text; const p=parseMutasi(text); applyParsed(p,text); speak((p.jenis||'')+' '+(p.barangLabel||'')+' '+(p.jumlah||'')+' — cek lalu simpan.'); }catch(err){ showNs(err.message||'Gagal transkripsi'); tx.textContent='Gagal — coba lagi'; } finally { if(streamRef){ streamRef.getTracks().forEach(t=>t.stop()); streamRef=null; } }
        }; mediaRecorder.start(); listening=true; modalBtn.classList.add('voice-mic-btn--on'); openBtn.classList.add('voice-mic-btn--on'); tx.textContent='Mendengarkan... tap lagi untuk kirim'; pv.textContent=''; }catch(err){ showNs('Izin mic ditolak — izinkan Mikrofon.'); listening=false; modalBtn.classList.remove('voice-mic-btn--on'); openBtn.classList.remove('voice-mic-btn--on'); }
    }
    function stopWhisper(){ try{ if(mediaRecorder&&mediaRecorder.state==='recording') mediaRecorder.stop(); }catch(e){} }
    modalBtn?.addEventListener('click', ()=>{
        if(useWhisper){ if(listening) stopWhisper(); else startWhisper(); return; }
        if(rec){ if(listening){ try{ rec.stop(); }catch(e){} } else { try{ hideNs(); rec.start(); }catch(e){} } }
        else { if(listening) stopWhisper(); else startWhisper(); }
    });
})();
</script>
@endif
@endsection
