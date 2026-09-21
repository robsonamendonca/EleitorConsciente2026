<?php
$pageTitle = 'Fontes Oficiais de Dados - Eleitor Consciente 2026';
require __DIR__ . '/layout/header.php';
?>

<div style="margin-bottom: 1.5rem;">
    <h1>Fontes Oficiais e Institucionais</h1>
    <p style="color:var(--text-muted); text-transform:uppercase; font-size:0.85rem;">
        A integridade das informações apresentadas pelo Eleitor Consciente depende do uso exclusivo de fontes governamentais primárias ou dados oficiais auditáveis.
    </p>
</div>

<div style="display:flex; flex-direction:column; gap:1rem;">
    <?php foreach ($sources as $src): ?>
        <div class="card" style="border-left: 6px solid var(--btn-confirma);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:0.5rem;">
                <div>
                    <span style="font-size:0.65rem; font-weight:900; background:var(--status-deferido-bg); color:var(--status-deferido-text); padding:2px 8px; border-radius:2px; text-transform:uppercase;">
                        <?= $e($src['authority_level']) ?> (<?= $e($src['source_type']) ?>)
                    </span>
                    <h2 style="font-size:1.15rem; margin:0.3rem 0 0 0;"><?= $e($src['name']) ?></h2>
                </div>
                <a href="<?= $e($src['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-secondary">
                    Acessar Fonte Oficial &rarr;
                </a>
            </div>
            <p style="font-size:0.8rem; color:var(--text-muted); margin:0.4rem 0 0 0; text-transform:uppercase;">
                <strong>URL Base:</strong> <a href="<?= $e($src['url']) ?>" target="_blank" rel="noopener noreferrer"><?= $e($src['url']) ?></a>
            </p>
        </div>
    <?php endforeach; ?>
</div>

<div class="card" style="margin-top: 1.5rem; background-color:#ffffff; border-left:6px solid var(--btn-teclas);">
    <h3 style="font-size:1rem; margin-bottom:0.3rem;">Política de Não Invenção de Dados (PRD Seção 5)</h3>
    <ul style="padding-left:1.25rem; font-size:0.8rem; color:var(--text-muted); line-height:1.7; text-transform:uppercase;">
        <li>Nenhum dado é inserido sem origem comprovada em registros públicos oficiais.</li>
        <li>Informações inexistentes ou pendentes no TSE são mantidas expressamente como não informadas, jamais preenchidas com suposições.</li>
        <li>O pipeline calcula e audita o hash SHA-256 de cada arquivo governamental consumido.</li>
    </ul>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
