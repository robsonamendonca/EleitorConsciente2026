/**
 * Lógica principal da interface Web do Eleitor Consciente 2026
 * Design System: Urna Eletrônica Brasileira
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Atualização do contador da Cola Eleitoral no menu
    function updateColaBadge() {
        const badge = document.getElementById('nav-cola-count');
        if (badge && window.ColaEleitoral) {
            const count = window.ColaEleitoral.count();
            badge.textContent = count > 0 ? ` (${count})` : '';
        }
    }

    updateColaBadge();
    window.addEventListener('cola:updated', updateColaBadge);

    // 2. Intercepta cliques de botões "Adicionar à Cola"
    document.querySelectorAll('.btn-add-cola').forEach(btn => {
        const candidateId = Number(btn.getAttribute('data-id'));
        if (window.ColaEleitoral && window.ColaEleitoral.hasCandidate(candidateId)) {
            const slot = window.ColaEleitoral.findCandidateSlot(candidateId);
            btn.textContent = '✓ Na Minha Cola';
            btn.classList.add('btn-secondary');
            btn.classList.remove('btn-accent');
        }

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const data = {
                id: Number(btn.getAttribute('data-id')),
                office_code: btn.getAttribute('data-office-code'),
                office_name: btn.getAttribute('data-office-name'),
                ballot_number: btn.getAttribute('data-number'),
                ballot_name: btn.getAttribute('data-name'),
                party_acronym: btn.getAttribute('data-party'),
                photo_url: btn.getAttribute('data-photo') || null
            };

            window.ColaEleitoral.addCandidate(data);
            btn.textContent = '✓ Na Minha Cola';
            btn.classList.add('btn-secondary');
            btn.classList.remove('btn-accent');
            
            // Notificação leve
            const notif = document.getElementById('live-feedback');
            if (notif) {
                const officeLabel = data.office_code === 'SENADOR' ? 'Senador(a)' : data.office_name;
                notif.textContent = `Candidato(a) adicionado(a) para ${officeLabel}!`;
                notif.style.display = 'block';
                setTimeout(() => { notif.style.display = 'none'; }, 3000);
            }
        });
    });

    // 3. Renderização e ações na página da Cola Eleitoral (/cola-eleitoral)
    const checklistPage = document.getElementById('cola-page-container');
    if (checklistPage && window.ColaEleitoral) {
        function renderChecklist() {
            const cola = window.ColaEleitoral.get();
            const slots = document.querySelectorAll('.checklist-slot');

            slots.forEach(slot => {
                const code = slot.getAttribute('data-office-code');
                const cand = cola[code];
                const contentDiv = slot.querySelector('.slot-content');
                const noteInput = slot.querySelector('.slot-note');

                if (cand) {
                    slot.classList.add('filled');
                    const photoHtml = cand.photo_url
                        ? `<img src="${escapeHtml(cand.photo_url)}" alt="Foto de ${escapeHtml(cand.ballot_name)}" class="cola-photo" onerror="this.style.display='none'">`
                        : '';
                    contentDiv.innerHTML = `
                        <div class="cola-candidate-row">
                            ${photoHtml}
                            <div class="cola-candidate-info">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:0.5rem;">
                                    <div>
                                        <h4 style="margin:0; font-size:1.05rem; color:#000000; text-transform:uppercase;">${escapeHtml(cand.ballot_name)}</h4>
                                        <span style="font-size:0.8rem; color:#333333; text-transform:uppercase; font-weight:700;">${escapeHtml(cand.party_acronym)}</span>
                                    </div>
                                    <span class="candidate-number-badge">${escapeHtml(cand.ballot_number)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="no-print" style="margin-top:0.4rem;">
                            <button class="btn btn-sm btn-corrige btn-remove-slot" data-office-code="${code}">✕ Remover</button>
                        </div>
                    `;
                    if (noteInput) {
                        noteInput.value = cand.note || '';
                        noteInput.style.display = 'block';
                    }
                } else {
                    slot.classList.remove('filled');
                    const baseOffice = slot.getAttribute('data-base-office') || code;
                    contentDiv.innerHTML = `
                        <p style="color:#555555; font-style:italic; margin-bottom:0.4rem; text-transform:uppercase; font-size:0.85rem;">Nenhum candidato selecionado.</p>
                        <a href="/candidatos?office=${encodeURIComponent(baseOffice)}" class="btn btn-sm btn-secondary no-print">+ Buscar Candidatos</a>
                    `;
                    if (noteInput) {
                        noteInput.style.display = 'none';
                    }
                }
            });

            // Bind dos botões de remover
            document.querySelectorAll('.btn-remove-slot').forEach(b => {
                b.addEventListener('click', () => {
                    const c = b.getAttribute('data-office-code');
                    window.ColaEleitoral.removeCandidate(c);
                    renderChecklist();
                });
            });
        }

        renderChecklist();

        // Salva anotações pessoais
        document.querySelectorAll('.slot-note').forEach(input => {
            input.addEventListener('change', () => {
                const slot = input.closest('.checklist-slot');
                const code = slot.getAttribute('data-office-code');
                window.ColaEleitoral.updateNote(code, input.value);
            });
        });

        // Botão Imprimir
        const printBtn = document.getElementById('btn-print-cola');
        if (printBtn) {
            printBtn.addEventListener('click', () => window.print());
        }

        // Botão Limpar
        const clearBtn = document.getElementById('btn-clear-cola');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                if (confirm('Deseja realmente limpar toda a sua cola eleitoral deste navegador?')) {
                    window.ColaEleitoral.clear();
                    renderChecklist();
                }
            });
        }
    }
});

/**
 * Escapa HTML para prevenir XSS
 */
function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
