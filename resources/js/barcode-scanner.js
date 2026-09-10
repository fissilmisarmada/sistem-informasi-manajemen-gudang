// ponytail: native BarcodeDetector dulu, fallback html5-qrcode CDN bila tidak ada. Tambah format di FORMATS bila butuh.
const FORMATS = ['ean_13', 'ean_8', 'code_128', 'code_39', 'qr_code', 'upc_a', 'upc_e'];

export function isSecureContextOk() {
    return window.isSecureContext;
}

export async function createDetector() {
    if (!('BarcodeDetector' in window)) return null;
    try {
        if (typeof BarcodeDetector.getSupportedFormats === 'function') {
            const supported = await BarcodeDetector.getSupportedFormats();
            const use = FORMATS.filter(f => supported.includes(f));
            return new BarcodeDetector({ formats: use.length ? use : FORMATS });
        }
        return new BarcodeDetector({ formats: FORMATS });
    } catch {
        try { return new BarcodeDetector(); } catch { return null; }
    }
}

export async function startCamera(videoEl) {
    if (!isSecureContextOk()) throw new Error('Kamera butuh HTTPS atau localhost. Buka via https:// atau php artisan serve.');
    if (!navigator.mediaDevices?.getUserMedia) throw new Error('Browser tidak dukung kamera.');
    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' }, width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false });
    videoEl.srcObject = stream;
    videoEl.setAttribute('playsinline', 'true');
    videoEl.setAttribute('autoplay', 'true');
    videoEl.muted = true;
    await videoEl.play();
    return stream;
}

export function stopStream(stream, videoEl) {
    stream?.getTracks().forEach(t => t.stop());
    if (videoEl) videoEl.srcObject = null;
}

// Native loop; returns stop fn
export async function scanWithDetector(videoEl, onResult, onError) {
    const detector = await createDetector();
    if (!detector) throw new Error('NO_DETECTOR');
    let raf = 0, running = true;
    const loop = async () => {
        if (!running) return;
        try {
            if (videoEl.readyState >= 2) {
                const codes = await detector.detect(videoEl);
                if (codes.length) { onResult(codes[0].rawValue); return; }
            }
        } catch (e) { onError?.(e); }
        raf = requestAnimationFrame(loop);
    };
    loop();
    return () => { running = false; cancelAnimationFrame(raf); };
}

// Fallback html5-qrcode via CDN (lazy, no npm)
let html5Loaded = false;
async function loadHtml5Qrcode() {
    if (html5Loaded) return;
    if (window.Html5Qrcode) { html5Loaded = true; return; }
    await new Promise((res, rej) => {
        const s = document.createElement('script');
        s.src = 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js';
        s.onload = res; s.onerror = rej; document.head.appendChild(s);
    });
    html5Loaded = true;
}

export async function scanWithHtml5Qrcode(containerId, onResult, onError) {
    await loadHtml5Qrcode();
    const qr = new window.Html5Qrcode(containerId);
    await qr.start({ facingMode: 'environment' }, { fps: 10, qrbox: { width: 250, height: 250 } }, onResult, onError);
    return () => qr.stop().then(() => qr.clear()).catch(() => {});
}
