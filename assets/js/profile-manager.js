(function() {
    'use strict';

    var nonce = document.getElementById('pp_profile_nonce')?.value || '';
    var ajaxUrl = document.getElementById('pp_ajax_url')?.value || '';

    if (!nonce || !ajaxUrl) return;

    function sendRequest(action, profileId, btn) {
        var origText = btn.textContent;
        btn.disabled = true;
        btn.textContent = '...';

        var formData = new FormData();
        formData.append('action', action);
        formData.append('nonce', nonce);
        formData.append('profile_id', profileId);

        fetch(ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                if (action === 'pp_delete_profile') {
                    var card = btn.closest('.pp-profile-card');
                    if (card) card.remove();
                    showMessage('success', data.data.message || 'Profil törölve.');
                } else if (action === 'pp_clear_favorites') {
                    var card = btn.closest('.pp-profile-card');
                    if (card) {
                        var countEl = card.querySelector('.pp-fav-count');
                        if (countEl) countEl.textContent = '0';
                    }
                    showMessage('success', data.data.message || 'Kedvencek törölve.');
                } else if (action === 'pp_clear_watch_later') {
                    var card = btn.closest('.pp-profile-card');
                    if (card) {
                        var countEl = card.querySelector('.pp-wl-count');
                        if (countEl) countEl.textContent = '0';
                    }
                    showMessage('success', data.data.message || 'Megnézendők törölve.');
                }
            } else {
                showMessage('error', data.data.message || 'Hiba történt.');
                btn.disabled = false;
                btn.textContent = origText;
            }
        })
        .catch(function() {
            showMessage('error', 'Hálózati hiba.');
            btn.disabled = false;
            btn.textContent = origText;
        });
    }

    function showMessage(type, msg) {
        var el = document.getElementById('pp-profile-message');
        if (!el) return;
        el.style.display = 'block';
        el.textContent = msg;
        el.className = type === 'success' ? 'pp-msg-success' : 'pp-msg-error';
        el.style.padding = '10px 14px';
        el.style.borderRadius = '4px';
        if (type === 'success') {
            el.style.background = '#edfaef';
            el.style.border = '1px solid #108548';
            el.style.color = '#108548';
        } else {
            el.style.background = '#fcf0f1';
            el.style.border = '1px solid #d63638';
            el.style.color = '#d63638';
        }
        setTimeout(function() { el.style.display = 'none'; }, 4000);
    }

    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.pp-btn');
        if (!btn) return;

        var action = btn.dataset.action;
        var pid = btn.dataset.pid;
        if (!action || !pid) return;

        e.preventDefault();

        if (action === 'delete_profile') {
            var confirmed = confirm('Biztosan törlöd a teljes profilt? Ez a művelet nem visszavonható.');
            if (!confirmed) return;
        }

        var wpAction;
        switch (action) {
            case 'delete_profile':   wpAction = 'pp_delete_profile'; break;
            case 'clear_favorites':  wpAction = 'pp_clear_favorites'; break;
            case 'clear_watch_later': wpAction = 'pp_clear_watch_later'; break;
            default: return;
        }

        sendRequest(wpAction, pid, btn);
    });
})();
