import './bootstrap';

// ponytail: minimal progressive enhancement — upgrade to a proper
// component system if mobile interactions grow beyond scan + denah.
function initProfileMenu() {
    const btn = document.getElementById('profile-menu-btn');
    const menu = document.getElementById('profile-menu');
    if (!btn || !menu) return;
    const close = () => { btn.setAttribute('aria-expanded', 'false'); menu.hidden = true; };
    const open = () => { btn.setAttribute('aria-expanded', 'true'); menu.hidden = false; };
    btn.addEventListener('click', (e) => { e.stopPropagation(); menu.hidden ? open() : close(); });
    document.addEventListener('click', (e) => { if (!menu.contains(e.target) && e.target !== btn) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initProfileMenu);
else initProfileMenu();

function initWitaClock() {
    const timeEl = document.getElementById('wita-clock');
    const dateEl = document.getElementById('wita-date');
    if (!timeEl || !dateEl) return;
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const tick = () => {
        const now = new Date();
        // ponytail: WITA = Asia/Makassar UTC+8 fixed offset; Intl would need timeZone support — manual +8 keeps it simple.
        const wita = new Date(now.getTime() + (now.getTimezoneOffset() * 60000) + 8 * 3600000);
        dateEl.textContent = `${String(wita.getDate()).padStart(2, '0')} ${months[wita.getMonth()]} ${wita.getFullYear()}`;
        timeEl.innerHTML = `${String(wita.getHours()).padStart(2, '0')}:${String(wita.getMinutes()).padStart(2, '0')}<span class="site-header__time-seconds">:${String(wita.getSeconds()).padStart(2, '0')}</span>`;
        timeEl.dateTime = wita.toISOString();
    };
    tick();
    setInterval(tick, 1000);
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initWitaClock);
else initWitaClock();

// Auto-inject data-label on table cells so CSS card-mode can show headers.
function enhanceTables() {
    document.querySelectorAll('table').forEach(table => {
        const headers = [...table.querySelectorAll('thead th')].map(th => th.textContent.trim());
        if (!headers.length) return;
        table.querySelectorAll('tbody tr').forEach(tr => {
            [...tr.children].forEach((td, i) => {
                if (td.tagName === 'TD' && !td.hasAttribute('data-label') && headers[i]) {
                    td.setAttribute('data-label', headers[i]);
                }
                if (td.hasAttribute('colspan')) td.removeAttribute('data-label');
            });
        });
    });
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', enhanceTables);
else enhanceTables();

// Web Push: daftar service worker, subscribe/unsubscribe ke server, pantau status tombol.
function initNotifikasiWebPush() {
    const btn = document.getElementById('notifikasi-toggle');
    if (!btn) return;
    const meta = document.querySelector('meta[name="vapid-public-key"]');
    const supported = 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
    if (!supported || !meta || !meta.content) { btn.hidden = true; return; }

    const vapidKey = meta.content;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || navigator.standalone === true;

    const labels = { loading: 'Menyiapkan notifikasi…', off: 'Aktifkan notifikasi stok', on: 'Matikan notifikasi stok', blocked: 'Notifikasi diblokir browser' };
    const setState = (state) => {
        btn.dataset.state = state;
        btn.textContent = labels[state] || labels.off;
        btn.disabled = state === 'loading';
        const hint = document.getElementById('notifikasi-ios-hint');
        if (hint) hint.hidden = !(isIOS && !isStandalone && state !== 'loading' && state !== 'blocked');
    };

    const urlBase64ToUint8 = (b64) => {
        const pad = '='.repeat((4 - (b64.length % 4)) % 4);
        const bin = atob((b64 + pad).replace(/-/g, '+').replace(/_/g, '/'));
        const out = new Uint8Array(bin.length);
        for (let i = 0; i < bin.length; i++) out[i] = bin.charCodeAt(i);
        return out;
    };

    const post = (url, method, body) => fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify(body),
    });

    const refresh = async () => {
        if (Notification.permission === 'denied') return setState('blocked');
        try {
            const reg = await navigator.serviceWorker.ready;
            setState((await reg.pushManager.getSubscription()) ? 'on' : 'off');
        } catch (e) { setState('off'); }
    };

    navigator.serviceWorker.register('/sw.js').catch(() => {});

    btn.addEventListener('click', async () => {
        setState('loading');
        try {
            if (Notification.permission === 'default') await Notification.requestPermission();
            if (Notification.permission === 'denied') return setState('blocked');
            const reg = await navigator.serviceWorker.ready;
            const sub = await reg.pushManager.getSubscription();
            if (sub) {
                const res = await post('/notifikasi/unsubscribe', 'DELETE', { endpoint: sub.endpoint });
                if (res.ok) await sub.unsubscribe();
            } else {
                const newSub = await reg.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: urlBase64ToUint8(vapidKey) });
                const json = newSub.toJSON();
                const res = await post('/notifikasi/subscribe', 'POST', { endpoint: json.endpoint, keys: { p256dh: json.keys.p256dh, auth: json.keys.auth } });
                if (!res.ok) await newSub.unsubscribe(); // server menolak → jangan biarkan langganan lokal menggantung
            }
        } catch (e) { /* izin dibatalkan / jaringan gagal — label terakhir tetap mencerminkan kondisi */ }
        refresh();
    });

    refresh();
}
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initNotifikasiWebPush);
else initNotifikasiWebPush();
