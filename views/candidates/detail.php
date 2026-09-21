<?php
$pageTitle = $e($candidate['ballot_name']) . ' - Detalhes do Candidato';
require __DIR__ . '/../layout/header.php';
?>

<div style="margin-bottom: 0.75rem;">
    <a href="/candidatos" style="text-decoration:none; font-size:0.85rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">&larr; Voltar para a lista de candidatos</a>
</div>

<div class="candidate-detail-header">
    <img src="<?= $e($candidate['photo_url'] ?: '/assets/images/placeholder.svg') ?>"
         alt="Foto oficial de <?= $e($candidate['ballot_name']) ?>"
         class="candidate-detail-photo"
         onerror="this.src='/assets/images/placeholder.svg'">

    <div class="candidate-detail-title">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:0.75rem;">
            <div>
                <h1 style="margin:0 0 0.15rem 0; font-size:1.75rem;"><?= $e($candidate['ballot_name']) ?></h1>
                <p style="color:var(--text-muted); margin:0 0 0.4rem 0; font-size:0.95rem; text-transform:uppercase;"><?= $e($candidate['full_name']) ?></p>
            </div>
            <span class="candidate-number-badge" style="font-size:1.8rem; padding:4px 16px;">
                <?= $e($candidate['ballot_number']) ?>
            </span>
        </div>

        <div style="margin: 0.6rem 0; display:flex; gap:0.4rem; flex-wrap:wrap; align-items:center;">
            <span class="status-badge <?= (stripos($candidate['registration_status'], 'deferido') !== false) ? 'status-deferido' : 'status-pendente' ?>">
                Situação TSE: <?= $e($candidate['registration_status']) ?>
            </span>
            <span style="font-size:0.8rem; background:#ffffff; padding:2px 8px; border-radius:2px; border:1px solid var(--border-color); text-transform:uppercase;">
                ID TSE: <?= $e($candidate['tse_id']) ?>
            </span>
        </div>

        <div style="margin-top: 0.75rem; line-height: 1.8; text-transform:uppercase; font-size:0.9rem;">
            <p style="margin:0;"><strong>Cargo pretendido:</strong> <?= $e($candidate['office_name']) ?> (<?= $e($candidate['state_code'] === 'BR' ? 'Nacional' : 'Estado de São Paulo') ?>)</p>
            <p style="margin:0;"><strong>Partido:</strong> <?= $e($candidate['party_acronym']) ?><?= !empty($candidate['party_full_name']) ? ' - ' . $e($candidate['party_full_name']) : '' ?></p>
            <?php if (!empty($candidate['federation_name'])): ?>
                <p style="margin:0;"><strong>Federação / Coligação:</strong> <?= $e($candidate['federation_name']) ?></p>
            <?php endif; ?>
        </div>

        <div style="margin-top: 1rem;">
            <button class="btn btn-accent btn-add-cola"
                    data-id="<?= $candidate['id'] ?>"
                    data-office-code="<?= $e($candidate['office_code']) ?>"
                    data-office-name="<?= $e($candidate['office_name']) ?>"
                    data-number="<?= $e($candidate['ballot_number']) ?>"
                    data-name="<?= $e($candidate['ballot_name']) ?>"
                    data-party="<?= $e($candidate['party_acronym']) ?>">
                + Adicionar à Minha Cola Eleitoral
            </button>
        </div>
    </div>
</div>

<!-- Seção de Propostas -->
<div class="card">
    <h2 style="font-size:1.2rem;">Propostas Registradas</h2>
    <?php if (empty($proposals)): ?>
        <p style="color:var(--text-muted); font-style:italic; text-transform:uppercase; font-size:0.85rem;">
            O plano de governo deste candidato pode ser consultado integralmente na página oficial do DivulgaCandContas do TSE. Nenhuma proposta temática individual foi adicionada até o momento.
        </p>
        <a href="https://divulgacandcontas.tse.jus.br/divulga/#/candidato/2026/999/<?= urlencode($candidate['state_code']) ?>/<?= urlencode($candidate['tse_id']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-secondary">
            Ver Proposta no DivulgaCandContas TSE &rarr;
        </a>
    <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:0.75rem; margin-top:0.75rem;">
            <?php foreach ($proposals as $prop): ?>
                <div style="border-left: 4px solid var(--btn-teclas); padding-left:0.75rem;">
                    <span style="font-size:0.7rem; font-weight:900; color:var(--btn-teclas); text-transform:uppercase; letter-spacing:0.5px;">
                        <?= $e($prop['category']) ?>
                    </span>
                    <h3 style="margin:0.15rem 0; font-size:1rem;"><?= $e($prop['title']) ?></h3>
                    <p style="color:var(--text-muted); margin:0; text-transform:uppercase; font-size:0.85rem;"><?= nl2br($e($prop['description'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Seção de Histórico Público -->
<div class="card">
    <h2 style="font-size:1.2rem;">Histórico e Registros Públicos</h2>
    <?php if (empty($records)): ?>
        <p style="color:var(--text-muted); font-style:italic; text-transform:uppercase; font-size:0.85rem;">
            Nenhum histórico prévio de mandato ou projeto registrado nesta base até o momento. Dados públicos adicionais podem ser consultados nos portais da Câmara dos Deputados, Senado ou Assembleia Legislativa (ALESP).
        </p>
    <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:0.75rem; margin-top:0.75rem;">
            <?php foreach ($records as $rec): ?>
                <div style="border-bottom: 2px solid var(--border-color); padding-bottom:0.6rem;">
                    <span style="font-size:0.7rem; font-weight:900; color:var(--btn-confirma); text-transform:uppercase; letter-spacing:0.5px;">
                        <?= $e($rec['record_type']) ?> - <?= $e($rec['reference_date']) ?>
                    </span>
                    <h3 style="margin:0.15rem 0; font-size:1rem;"><?= $e($rec['title']) ?></h3>
                    <p style="color:var(--text-muted); margin:0; text-transform:uppercase; font-size:0.85rem;"><?= nl2br($e($rec['description'])) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Rastreabilidade e Auditoria das Fontes -->
<div class="card" style="background-color:#ffffff;">
    <h3 style="margin-bottom:0.4rem; font-size:1rem;">Fontes Auditáveis deste Registro</h3>
    <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:0.6rem; text-transform:uppercase;">
        Em conformidade com as Seções 4.2 e 4.5 do PRD, cada dado exibido nesta ficha possui rastreabilidade com a fonte oficial governamental:
    </p>

    <?php if (empty($sources)): ?>
        <div class="source-box">
            <strong>Fonte Primária:</strong> Dados Abertos do Tribunal Superior Eleitoral (TSE) - Consulta Cand 2026.<br>
            <strong>URL Oficial:</strong> <a href="https://dadosabertos.tse.jus.br/" target="_blank" rel="noopener noreferrer">https://dadosabertos.tse.jus.br/</a><br>
            <strong>Data da Coleta:</strong> <?= $e(date('d/m/Y H:i', strtotime($candidate['created_at']))) ?>
        </div>
    <?php else: ?>
        <?php foreach ($sources as $src): ?>
            <div class="source-box">
                <strong><?= $e($src['source_name']) ?></strong> (<?= $e($src['authority_level']) ?>)<br>
                <strong>Link Oficial:</strong> <a href="<?= $e($src['source_url']) ?>" target="_blank" rel="noopener noreferrer"><?= $e($src['source_url']) ?></a><br>
                <strong>Coletado em:</strong> <?= $e(date('d/m/Y H:i', strtotime($src['retrieved_at']))) ?><br>
                <?php if (!empty($src['content_hash'])): ?>
                    <span style="font-family:monospace; font-size:0.7rem;">SHA256: <?= $e($src['content_hash']) ?></span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
