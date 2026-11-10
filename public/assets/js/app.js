document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.alert').forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s';
            setTimeout(function () { alert.remove(); }, 300);
        }, 5000);
    });

    var resultadoSelect = document.getElementById('resultado');
    var followupFields = document.getElementById('followup-fields');
    if (resultadoSelect && followupFields) {
        function toggleFollowup() {
            followupFields.style.display = resultadoSelect.value === 'follow_up' ? 'block' : 'none';
        }
        resultadoSelect.addEventListener('change', toggleFollowup);
        toggleFollowup();
    }

    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(el.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    initAbordagemSelector();
    initKanban();
    initGatilhoResults();
});

function initGatilhoResults() {
    var whatsappLink = document.getElementById('whatsapp-link');
    var whatsappHint = document.getElementById('whatsapp-message-hint');

    function syncWhatsappMessage(preferredCheckbox) {
        if (!whatsappLink) return;

        var checked = preferredCheckbox && preferredCheckbox.checked
            ? preferredCheckbox
            : document.querySelector('[data-gatilho-checkbox]:checked');
        var message = checked ? checked.dataset.gatilhoText : whatsappLink.dataset.whatsappDefaultMessage;
        whatsappLink.href = whatsappLink.dataset.whatsappBaseUrl + '?text=' + encodeURIComponent(message || '');

        if (whatsappHint) {
            whatsappHint.textContent = checked
                ? 'Mensagem selecionada: ' + (checked.dataset.gatilhoTitle || 'gatilho') + '.'
                : 'Mensagem padrão selecionada.';
        }
    }

    document.querySelectorAll('.gatilho-card').forEach(function (card) {
        var checkbox = card.querySelector('[data-gatilho-checkbox]');
        var result = card.querySelector('[data-gatilho-resultado]');
        if (!checkbox || !result) return;

        function sync() {
            result.disabled = !checkbox.checked;
            if (!checkbox.checked) result.value = '';
        }

        checkbox.addEventListener('change', function () {
            sync();
            syncWhatsappMessage(checkbox);
        });
        sync();
    });

    syncWhatsappMessage();
}

function initAbordagemSelector() {
    var box = document.getElementById('abordagem-box');
    if (!box) return;

    var tipoInput = document.getElementById('abordagem_tipo');
    var textoInput = document.getElementById('abordagem_texto');
    var opcoes = {};
    try {
        opcoes = JSON.parse(box.dataset.opcoes || '{}');
    } catch (e) {
        opcoes = {};
    }

    box.querySelectorAll('.abordagem-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            var tipo = tab.dataset.tipo;
            box.querySelectorAll('.abordagem-tab').forEach(function (t) {
                t.classList.toggle('is-active', t === tab);
                t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
            });
            tipoInput.value = tipo;
            if (opcoes[tipo] && opcoes[tipo].texto) {
                textoInput.value = opcoes[tipo].texto;
            }
        });
    });
}

function initKanban() {
    var board = document.getElementById('kanban-board');
    if (!board) return;

    var csrf = board.dataset.csrf;
    var baseUrl = board.dataset.etapaUrl;
    var dragging = null;

    board.querySelectorAll('.kanban-card').forEach(function (card) {
        card.addEventListener('dragstart', function () {
            dragging = card;
            card.classList.add('is-dragging');
        });
        card.addEventListener('dragend', function () {
            card.classList.remove('is-dragging');
            dragging = null;
            board.querySelectorAll('.kanban-column__body').forEach(function (z) {
                z.classList.remove('is-over');
            });
        });
    });

    board.querySelectorAll('.kanban-column__body').forEach(function (zone) {
        zone.addEventListener('dragover', function (e) {
            e.preventDefault();
            zone.classList.add('is-over');
        });
        zone.addEventListener('dragleave', function () {
            zone.classList.remove('is-over');
        });
        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            zone.classList.remove('is-over');
            if (!dragging) return;

            var novaEtapa = zone.dataset.dropzone;
            var leadId = dragging.dataset.leadId;
            var etapaAtual = dragging.dataset.etapa;
            if (!novaEtapa || !leadId || novaEtapa === etapaAtual) {
                if (novaEtapa === etapaAtual) zone.appendChild(dragging);
                return;
            }

            zone.appendChild(dragging);
            dragging.dataset.etapa = novaEtapa;

            var body = new URLSearchParams();
            body.set('_csrf', csrf);
            body.set('etapa_funil', novaEtapa);

            fetch(baseUrl + '/' + leadId + '/etapa', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-Token': csrf
                },
                body: body,
                credentials: 'same-origin'
            }).then(function (res) {
                return res.json().then(function (data) {
                    if (!res.ok || !data.ok) {
                        throw new Error(data.error || 'Falha ao mover lead');
                    }
                    updateColumnCounts();
                });
            }).catch(function (err) {
                alert(err.message || 'Não foi possível atualizar a etapa.');
                window.location.reload();
            });
        });
    });

    function updateColumnCounts() {
        board.querySelectorAll('.kanban-column').forEach(function (col) {
            var count = col.querySelectorAll('.kanban-card').length;
            var badge = col.querySelector('.kanban-column__head .badge');
            if (badge) badge.textContent = String(count);
        });
    }
}
