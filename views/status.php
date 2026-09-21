<?php
$pageTitle = 'Status dos Dados e Auditoria - Eleitor Consciente 2026';
require __DIR__ . '/layout/header.php';
?>

<div style="margin-bottom: 2rem;">
    <h1>Status dos Dados e Auditoria</h1>
    <p style="color:var(--text-muted); font-size:1.05rem;">
        Transparência em tempo real sobre a saúde do sistema e o histórico de importações de dados oficiais.
    </p>
</div>

<div class="offices-grid" style="margin-bottom: 2rem;">
    <div class="card" style="text-align:center;">
        <span style="font-size:2rem; font-weight:800; color:var(--primary);"><?= (int)$stats['total_candidates'] ?></span>
        <p style="margin:0; font-size:0.9rem; color:var(--text-muted);">Candidatos Cadastrados</p>
    </div>
    <div class="card" style="text-align:center;">
        <span style="font-size:2rem; font-weight:800; color:var(--secondary);"><?= (int)$stats['total_parties'] ?></span>
        <p style="margin:0; font-size:0.9rem; color:var(--text-muted);">Partidos Oficiais</p>
    </div>
    <div class="card" style="text-align:center;">
        <span style="font-size:1.1rem; font-weight:700; color:var(--text-main); display:block; margin-top:0.6rem;">
            <?= $e($stats['last_sync'] ? date('d/m/Y H:i', strtotime($stats['last_sync'])) : 'Não executada') ?>
        </span>
        <p style="margin:0; font-size:0.9rem; color:var(--text-muted);">Última Sincronização Concluída</p>
    </div>
</div>

<div class="card">
    <h2>Histórico de Importações Auditadas</h2>
    <?php if (empty($imports)): ?>
        <p style="color:var(--text-muted); font-style:italic;">Nenhum registro de importação encontrado no banco de dados.</p>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:0.88rem; text-align:left;">
                <thead>
                    <tr style="border-bottom:2px solid var(--border-color); background:var(--bg-alt);">
                        <th style="padding:8px;">ID</th>
                        <th style="padding:8px;">Conjunto de Dados</th>
                        <th style="padding:8px;">Arquivo / Hash</th>
                        <th style="padding:8px;">Início</th>
                        <th style="padding:8px;">Processados</th>
                        <th style="padding:8px;">Inseridos</th>
                        <th style="padding:8px;">Rejeitados</th>
                        <th style="padding:8px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($imports as $imp): ?>
                        <tr style="border-bottom:1px solid var(--border-subtle);">
                            <td style="padding:8px;">#<?= $imp['id'] ?></td>
                            <td style="padding:8px; font-weight:600;"><?= $e($imp['dataset_name']) ?></td>
                            <td style="padding:8px;">
                                <span style="font-family:monospace; font-size:0.78rem;"><?= $e(substr($imp['file_hash'], 0, 12)) ?>...</span>
                            </td>
                            <td style="padding:8px;"><?= $e(date('d/m/Y H:i', strtotime($imp['started_at']))) ?></td>
                            <td style="padding:8px;"><?= (int)$imp['records_processed'] ?></td>
                            <td style="padding:8px; color:var(--status-deferido-text); font-weight:600;">+<?= (int)$imp['records_inserted'] ?></td>
                            <td style="padding:8px; color:<?= $imp['records_rejected'] > 0 ? 'var(--status-indeferido-text)' : 'inherit' ?>;"><?= (int)$imp['records_rejected'] ?></td>
                            <td style="padding:8px;">
                                <span class="status-badge <?= $imp['status'] === 'completed' ? 'status-deferido' : ($imp['status'] === 'failed' ? 'status-indeferido' : 'status-pendente') ?>">
                                    <?= $e($imp['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>

