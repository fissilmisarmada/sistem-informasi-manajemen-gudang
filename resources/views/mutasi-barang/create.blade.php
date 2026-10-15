@extends('layouts.app')

@section('content')
<style>
    .mutation-page { max-width:720px; margin:0 auto; }
    .card { background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); padding:28px; max-width:560px; margin:0 auto; transition: box-shadow .22s cubic-bezier(.16,1,.3,1); }
    .card:hover { box-shadow:0 10px 28px rgba(15,23,42,.08), 0 2px 6px rgba(15,23,42,.05); }
    .form-group { margin-bottom:18px; }
    label { display:block; font-size:11px; font-weight:700; color:var(--andon-muted); margin-bottom:8px; letter-spacing:.06em; }
    select, input[type=number], textarea { width:100%; padding:12px 14px; border:1px solid #E8EAF0; border-radius:14px; font-size:14px; background:#FBFBFD; color:var(--andon-ink); box-sizing:border-box; transition: border-color .18s ease, box-shadow .18s ease, background .18s ease; }
    select:focus, input:focus, textarea:focus { outline:none; border-color:var(--andon-ink); box-shadow:0 0 0 3px rgba(15,23,42,.06); background:#fff; }
    .form-actions { display:flex; gap:10px; margin-top:24px; }
    .alert-error { background:#FEF2F2; color:#7F1D1D; border:1px solid #FECACA; padding:10px 14px; border-radius:14px; margin-bottom:16px; font-size:13px; }
    .stok-info { background:#FBFBFD; border:1px solid #EDEEF2; border-radius:14px; padding:11px 14px; font-size:13px; margin-top:10px; display:none; color:var(--andon-muted); }
    .jenis-toggle { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
    .jenis-btn { padding:13px; border:1px solid #E8EAF0; border-radius:14px; text-align:center; cursor:pointer; font-weight:800; font-size:13px; transition: all .2s cubic-bezier(.16,1,.3,1); background:#FBFBFD; color:var(--andon-muted); }
    .jenis-btn:hover { border-color: var(--andon-line-strong); transform: translateY(-1px); }
    .jenis-btn.masuk.active { border-color: var(--andon-green); background: #F0FDF4; color: #14532D; box-shadow: 0 4px 12px rgba(22,163,74,.12); }
    .jenis-btn.keluar.active { border-color: var(--andon-red); background: #FEF2F2; color: #7F1D1D; box-shadow: 0 4px 12px rgba(220,38,38,.1); }
    input[type=radio] { display:none; }
    .voice-bar{display:flex;gap:10px;align-items:center;flex-wrap:wrap;background:#F8FAFC;border:1px solid #E2E8F0;border-radius:14px;padding:10px 12px;margin-bottom:16px}
    .voice-btn{width:44px;height:44px;border-radius:999px;display:grid;place-items:center;border:1px solid var(--andon-line-strong);background:#fff;cursor:pointer;flex:0 0 auto;transition:transform .14s,box-shadow .16s,background .16s}
    .voice-btn:hover{transform:translateY(-1px);box-shadow:var(--andon-shadow)}
    .voice-btn--on{background:var(--andon-amber);border-color:var(--andon-amber-dim);box-shadow:0 0 0 4px var(--andon-amber-glow);animation:voice-pulse 1.1s infinite}
    @keyframes voice-pulse{0%,100%{box-shadow:0 0 0 4px var(--andon-amber-glow)}50%{box-shadow:0 0 0 7px rgba(239,131,84,.10)}}
    .voice-copy{flex:1;min-width:160px}
    .voice-transcript{font-size:12px;color:var(--andon-ink);background:#fff;border:1px solid var(--andon-line);border-radius:10px;padding:8px 10px;min-height:36px;word-break:break-word}
    .voice-preview{font-size:11px;color:var(--andon-muted);margin-top:6px;line-height:1.4}
    .voice-preview strong{color:var(--andon-ink)}
    .voice-hint{font-size:10px;color:var(--andon-faint);margin-top:4px}
    .voice-nosupport{font-size:11px;font-weight:700;color:#991B1B;background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:8px 10px}
    @media(prefers-reduced-motion:reduce){.voice-btn--on{animation:none}}
</style>

<div class="mutation-page">
<div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px">
    <a href="{{ request('barang_id') ? route('barang.show', request('barang_id')) : route('mutasi-barang.index') }}" class="back-link" aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
    <h1 class="andon-page-title" style="margin:0">Catat Mutasi Barang</h1>
</div>

<!-- CARD DENGAN FORM -->
<div class="card">
    @if($errors->any())
        <div class="alert-error">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="voice-bar" id="voice-bar" role="region" aria-label="Input suara mutasi">
        <button type="button" class="voice-btn" id="voice-btn" aria-label="Mulai rekam suara" title="Ucapkan: masuk/keluar KODE jumlah — contoh: masuk ATK 001 sepuluh">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18" aria-hidden="true"><path d="M12 14a3 3 0 003-3V6a3 3 0 10-6 0v5a3 3 0 003 3z"/><path d="M19 10a7 7 0 01-14 0"/><path d="M12 19v3"/><path d="M8 22h8"/></svg>
        </button>
        <div class="voice-copy">
            <div class="voice-transcript" id="voice-transcript" aria-live="polite">Tekan mic lalu ucapkan — contoh: <em>"masuk ATK 001 sepuluh"</em> atau <em>"keluar kertas HVS 5"</em></div>
            <div class="voice-preview" id="voice-preview"></div>
            <div class="voice-hint">Hanya mengisi form, tidak otomatis simpan — cek dulu baru Simpan Mutasi. Chrome/Edge, izin mic.</div>
        </div>
        <button type="button" class="btn btn--ghost" id="voice-speak" style="min-height:36px;white-space:nowrap">Bacakan</button>
    </div>
    <div class="voice-nosupport" id="voice-nosupport" style="display:none">Browser ini belum dukung input suara. Gunakan Chrome/Edge terbaru.</div>

    <form method="POST" action="{{ route('mutasi-barang.store') }}">
        @csrf

        <div class="form-group">
            <label>Barang <span style="color:var(--andon-red)">*</span></label>
            <select name="barang_id" id="barang-select" required onchange="updateStokInfo()">
                <option value="">-- Pilih Barang --</option>
                @foreach($barangs as $b)
                    <option value="{{ $b->id }}"
                        data-stok="{{ $b->stok }}"
                        data-satuan="{{ $b->satuan }}"
                        data-nama="{{ $b->nama }}"
                        {{ old('barang_id', request('barang_id')) == $b->id ? 'selected' : '' }}>
                        {{ $b->nama }} ({{ $b->kategori->nama }})
                    </option>
                @endforeach
            </select>
            <div class="stok-info" id="stok-info"></div>
        </div>

        <div class="form-group">
            <label>Jenis Mutasi <span style="color:var(--andon-red)">*</span></label>
            <div class="jenis-toggle">
                <label class="jenis-btn masuk {{ old('jenis') === 'masuk' ? 'active' : '' }}" onclick="setJenis('masuk')">
                    <input type="radio" name="jenis" value="masuk" {{ old('jenis') === 'masuk' ? 'checked' : '' }}>
                    ↑ Barang Masuk
                </label>
                <label class="jenis-btn keluar {{ old('jenis') === 'keluar' ? 'active' : '' }}" onclick="setJenis('keluar')">
                    <input type="radio" name="jenis" value="keluar" {{ old('jenis') === 'keluar' ? 'checked' : '' }}>
                    ↓ Barang Keluar
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Jumlah <span style="color:var(--andon-red)">*</span></label>
            <input type="number" name="jumlah" id="jumlah-input" value="{{ old('jumlah', 1) }}" min="1" required>
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" rows="3" placeholder="Contoh: Pembelian dari supplier, Digunakan untuk kegiatan X...">{{ old('keterangan') }}</textarea>
        </div>

        <!-- SATU-SATUNYA form-actions -->
        <div class="form-actions">
    @php
        $cancelUrl = request('barang_id')
            ? route('barang.show', request('barang_id'))
            : (request('from') === 'dashboard'
                ? (auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')))
                : route('mutasi-barang.index'));
    @endphp
    <a href="{{ $cancelUrl }}" class="btn btn--ghost">Batal</a>
    <button type="submit" class="btn btn--primary">Simpan Mutasi</button>
</div>
    </form>
</div>
</div>

<script>
function updateStokInfo() {
    const sel = document.getElementById('barang-select');
    const opt = sel.options[sel.selectedIndex];
    const info = document.getElementById('stok-info');
    if (sel.value) {
        info.style.display = 'block';
        info.innerHTML = `Stok saat ini: <strong style="color:var(--andon-ink)">${opt.dataset.stok} ${opt.dataset.satuan}</strong>`;
    } else {
        info.style.display = 'none';
    }
}
function setJenis(jenis) {
    document.querySelectorAll('.jenis-btn').forEach(el => el.classList.remove('active'));
    document.querySelector(`.jenis-btn.${jenis}`).classList.add('active');
    document.querySelector(`input[value="${jenis}"]`).checked = true;
}
document.addEventListener('DOMContentLoaded', updateStokInfo);
// voice → form: Web Speech (desktop/Android) + Whisper via MediaRecorder (iPhone/Chrome iOS) — ponytail: ganti Whisper lokal when OPENAI_API_KEY mahal
(function(){
    const btn=document.getElementById('voice-btn'), tx=document.getElementById('voice-transcript'), pv=document.getElementById('voice-preview'), ns=document.getElementById('voice-nosupport'), speakBtn=document.getElementById('voice-speak');
    const sel=document.getElementById('barang-select'), qty=document.getElementById('jumlah-input');
    const transcribeUrl="{{ route('voice.transcribe') }}";
    const csrf=document.querySelector('meta[name="csrf-token"]')?.content || '';
    const SR=window.SpeechRecognition||window.webkitSpeechRecognition;
    const isIOS=/iPad|iPhone|iPod/.test(navigator.userAgent);
    const hasMediaRecorder=!!window.MediaRecorder && !!navigator.mediaDevices?.getUserMedia;
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
        if(lastMasuk>=0 || lastKeluar>=0) jenis = lastKeluar>lastMasuk ? 'keluar' : 'masuk';
        const numMatch=raw.match(/(\d+|satu|dua|tiga|empat|lima|enam|tujuh|delapan|sembilan|sepuluh|sebelas|dua belas|tiga belas|empat belas|lima belas|enam belas|tujuh belas|delapan belas|sembilan belas|dua puluh|tiga puluh|empat puluh|lima puluh|seratus|seribu)(?:\b|$)/);
        const jumlah=numMatch ? (wordsToNum(numMatch[1]) ?? parseInt(numMatch[1],10)) : null;
        let barangId=null, barangLabel=null, cand=[];
        const kodeHit=raw.match(/\b([a-z]{2,4})\s*[- ]?\s*(\d{1,4})\b/i);
        if(kodeHit){ const kode=(kodeHit[1]+'-'+kodeHit[2]).toUpperCase(); for(const o of sel.options){ if(o.value && o.textContent.toUpperCase().includes(kode)){ barangId=o.value; barangLabel=o.textContent.trim(); break; } } }
        if(!barangId){
            const rawTokens=norm(raw).split(' ').filter(Boolean);
            let bestScore=0, best=null;
            for(const o of sel.options){ if(!o.value) continue; const nm=(o.dataset.nama||o.textContent||'').toLowerCase(); const nmTokens=norm(nm).split(' ').filter(Boolean); if(!nmTokens.length) continue; let hit=0; for(const t of nmTokens){ if(rawTokens.includes(t)) hit++; } const score=hit/nmTokens.length; cand.push({o,score,label:o.textContent.trim()}); if(score>bestScore){ bestScore=score; best=o; } }
            cand.sort((a,b)=>b.score-a.score); if(best && bestScore>=0.5){ barangId=best.value; barangLabel=best.textContent.trim(); }
        }
        const _cand=cand.slice(0,3).filter(c=>c.score>0);
        return {jenis, jumlah, barangId, barangLabel, raw, _cand};
    }
    function applyParsed(p, rawText){
        let applied=[];
        if(p.barangId){ sel.value=p.barangId; sel.dispatchEvent(new Event('change')); updateStokInfo(); applied.push('Barang: <strong>'+p.barangLabel+'</strong>'); }
        if(p.jenis){ setJenis(p.jenis); applied.push('Jenis: <strong>'+p.jenis+'</strong>'); }
        if(p.jumlah && p.jumlah>=1){ qty.value=p.jumlah; applied.push('Jumlah: <strong>'+p.jumlah+'</strong>'); }
        if(applied.length) pv.innerHTML=applied.join(' · ') + '<br><span style="color:var(--andon-muted)">"'+rawText+'"</span> — cek lalu Simpan.';
        else { let sug=(p._cand||[]).map(c=>c.label).join(', '); pv.innerHTML='<span style="color:#991B1B">Tidak mengenali.</span> Coba: "masuk kertas HVS 10" atau "keluar pulpen sepuluh".' + (sug? '<br><span style="color:var(--andon-muted)">Mirip: '+sug+'</span>' : ''); }
        if(pv.textContent) pv.scrollIntoView({behavior:'smooth',block:'nearest'});
    }
    function speak(text){ try{ const u=new SpeechSynthesisUtterance(text); u.lang='id-ID'; u.rate=1; speechSynthesis.speak(u);}catch(e){} }
    function showNs(html){ ns.innerHTML=html; ns.style.display='block'; }
    function hideNs(){ ns.style.display='none'; ns.textContent=''; }
    if(!window.isSecureContext) showNs('Mic butuh HTTPS — ngrok sudah OK. Jika buka via http://IP, ganti ke https ngrok.');
    // state
    let listening=false, rec=null, mediaRecorder=null, chunks=[], streamRef=null, useWhisper=isIOS || !SR;
    // init SR if available and not forced whisper
    if(SR && !useWhisper){
        rec=new SR(); rec.lang='id-ID'; rec.interimResults=true; rec.continuous=false; rec.maxAlternatives=1;
        rec.onstart=()=>{ listening=true; btn.classList.add('voice-btn--on'); btn.setAttribute('aria-label','Berhenti merekam'); tx.textContent='Mendengarkan...'; pv.textContent=''; hideNs(); };
        rec.onend=()=>{ listening=false; btn.classList.remove('voice-btn--on'); btn.setAttribute('aria-label','Mulai rekam suara'); if(tx.textContent==='Mendengarkan...') tx.textContent='Tekan mic lalu ucapkan — contoh: "masuk ATK 001 sepuluh"'; };
        rec.onerror=(e)=>{
            listening=false; btn.classList.remove('voice-btn--on');
            const code=e.error||'';
            const map={ 'not-allowed':'Izin mic ditolak — tap ikon gembok > Mikrofon > Izinkan lalu reload.', 'no-speech':'Tidak ada suara terdeteksi — coba lebih dekat/dekat mic.', 'audio-capture':'Mic dipakai app lain.', 'network':'Butuh internet untuk pengenalan suara.', 'aborted':'Rekaman dibatalkan.' };
            if(map[code]) showNs(map[code]); else if(code) showNs('Gagal mic: '+code);
        };
        rec.onresult=(e)=>{
            let final='', interim=''; for(let i=e.resultIndex;i<e.results.length;i++){ const r=e.results[i]; if(r.isFinal) final+=r[0].transcript+' '; else interim+=r[0].transcript; }
            const text=(final||interim).trim(); if(text) tx.textContent=text;
            if(e.results[e.results.length-1].isFinal && final.trim()){ const p=parseMutasi(final.trim()); applyParsed(p, final.trim()); speak((p.jenis||'')+' '+(p.barangLabel||'')+' '+(p.jumlah||'')+' — cek lalu simpan.'); }
        };
    }
    // fallback: iPhone / no SR → Whisper
    if(!SR || useWhisper){
        if(!hasMediaRecorder){ showNs('Browser ini belum dukung rekam. Di iPhone: tap kolom teks mana saja → ikon mic di keyboard → ucap "masuk kertas HVS 5".'); }
        else if(isIOS || !SR){ tx.textContent='Tap mic → ucap "masuk kertas HVS 10" → lepas mic (iPhone via Whisper).'; }
    }
    async function startWhisper(){
        try{ hideNs(); const s=await navigator.mediaDevices.getUserMedia({audio:true}); streamRef=s; chunks=[]; let mime='audio/webm'; if(!MediaRecorder.isTypeSupported(mime)) mime='audio/mp4'; mediaRecorder=new MediaRecorder(s, mime?{mimeType:mime}:undefined); mediaRecorder.ondataavailable=e=>{ if(e.data.size) chunks.push(e.data); }; mediaRecorder.onstop=async()=>{
            listening=false; btn.classList.remove('voice-btn--on'); tx.textContent='Menerjemahkan...';
            try{ const blob=new Blob(chunks, {type: mediaRecorder.mimeType||'audio/webm'}); const fd=new FormData(); fd.append('audio', blob, 'voice.webm'); const res=await fetch(transcribeUrl,{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'},body:fd}); const j=await res.json(); if(!res.ok) throw new Error(j.error||'Gagal transkripsi'); const text=(j.text||'').trim(); if(!text) throw new Error('Tidak terdengar — coba lagi lebih jelas.'); tx.textContent=text; const p=parseMutasi(text); applyParsed(p,text); speak((p.jenis||'')+' '+(p.barangLabel||'')+' '+(p.jumlah||'')+' — cek lalu simpan.'); }catch(err){ showNs(err.message||'Gagal transkripsi'); tx.textContent='Gagal — coba lagi'; } finally { if(streamRef){ streamRef.getTracks().forEach(t=>t.stop()); streamRef=null; } }
        }; mediaRecorder.start(); listening=true; btn.classList.add('voice-btn--on'); tx.textContent='Mendengarkan... tap lagi untuk kirim'; pv.textContent=''; }catch(err){ showNs('Izin mic ditolak — izinkan Mikrofon di pengaturan browser.'); listening=false; btn.classList.remove('voice-btn--on'); }
    }
    function stopWhisper(){ try{ if(mediaRecorder&&mediaRecorder.state==='recording') mediaRecorder.stop(); }catch(e){} }
    btn.addEventListener('click', ()=>{
        if(useWhisper){
            if(listening) stopWhisper(); else startWhisper(); return;
        }
        if(rec){ if(listening){ try{ rec.stop(); }catch(e){} } else { try{ hideNs(); rec.start(); }catch(e){} } }
        else { if(hasMediaRecorder){ if(listening) stopWhisper(); else startWhisper(); } }
    });
    speakBtn.addEventListener('click', ()=>{ const p=parseMutasi(tx.textContent||''); let t='Belum ada input suara.'; if(p.barangLabel||p.jenis||p.jumlah) t=(p.jenis? p.jenis+' ':'')+(p.barangLabel? p.barangLabel+' ':'')+(p.jumlah? String(p.jumlah):'')+', cek form lalu simpan.'; speak(t); });
})();
</script>
@endsection
