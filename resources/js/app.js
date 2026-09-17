import './bootstrap';

// ponytail: minimal progressive enhancement — upgrade to a proper
// component system if mobile interactions grow beyond scan + denah.

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
