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
