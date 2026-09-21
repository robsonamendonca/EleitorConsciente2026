<?php
$pageTitle = 'Fontes Oficiais de Dados - Eleitor Consciente 2026';
require __DIR__ . '/layout/header.php';
?>

<div style="margin-bottom: 2rem;">
    <h1>Fontes Oficiais e Institucionais</h1>
    <p style="color:var(--text-muted); font-size:1.05rem;">
        A integridade das informações apresentadas pelo Eleitor Consciente depende do uso exclusivo de fontes governamentais primárias ou dados oficiais auditáveis.
    </p>
</div>

<div style="display:flex; flex-direction:column; gap:1.25rem;">
    <?php foreach ($sources as $src): ?>
        <div class="card" style="border-left: 4px solid var(--secondary);">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:0.5rem;">
                <div>
                    <span style="font-size:0.75rem; font-weight:800; background:var(--status-deferido-bg); color:var(--status-deferido-text); padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                        <?= $e($src['authority_level']) ?> (<?= $e($src['source_type']) ?>)
                    </span>
                    <h2 style="font-size:1.25rem; margin:0.4rem 0;"><?= $e($src['name']) ?></h2>
                </div>
                <a href="<?= $e($src['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-secondary">
                    Acessar Fonte Oficial &rarr;
                </a>
            </div>
            <p style="font-size:0.9rem; color:var(--text-muted); margin:0.5rem 0 0 0;">
                <strong>URL Base:</strong> <a href="<?= $e($src['url']) ?>" target="_blank" rel="noopener noreferrer"><?= $e($src['url']) ?></a>
            </p>
        </div>
    <?php endforeach; ?>
</div>

<div class="card" style="margin-top: 2rem; background-color:var(--bg-alt);">
    <h3>Política de Não Invenção de Dados (PRD Seção 5)</h3>
    <ul style="padding-left:1.25rem; font-size:0.9rem; color:var(--text-muted); line-height:1.7;">
        <li>Nenhum dado é inserido sem origem comprovada em registros públicos oficiais.</li>
        <li>Informações inexistentes ou pendentes no TSE são mantidas expressamente como não informadas, jamais preenchidas com suposições.</li>
        <li>O pipeline calcula e audita o hash SHA-256 de cada arquivo governamental consumido.</li>
    </ul>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>

