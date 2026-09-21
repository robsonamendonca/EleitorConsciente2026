<?php
$pageTitle = 'Status dos Dados e Auditoria - Eleitor Consciente 2026';
require __DIR__ . '/layout/header.php';
?>

<div style="margin-bottom: 1.5rem;">
    <h1>Status dos Dados e Auditoria</h1>
    <p style="color:var(--text-muted); text-transform:uppercase; font-size:0.85rem;">
        Transparência em tempo real sobre a saúde do sistema e o histórico de importações de dados oficiais.
    </p>
</div>

<div class="offices-grid" style="margin-bottom: 1.5rem;">
    <div class="card" style="text-align:center;">
        <span style="font-size:2.5rem; font-weight:900; color:var(--btn-teclas); font-family:var(--font-urna);"><?= (int)$stats['total_candidates'] ?></span>
        <p style="margin:0; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Candidatos Cadastrados</p>
    </div>
    <div class="card" style="text-align:center;">
        <span style="font-size:2.5rem; font-weight:900; color:var(--btn-teclas); font-family:var(--font-urna);"><?= (int)$stats['total_parties'] ?></span>
        <p style="margin:0; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Partidos Oficiais</p>
    </div>
    <div class="card" style="text-align:center;">
        <span style="font-size:1rem; font-weight:700; color:var(--text-main); display:block; margin-top:0.4rem; text-transform:uppercase;">
            <?= $e($stats['last_sync'] ? date('d/m/Y H:i', strtotime($stats['last_sync'])) : 'Não executada') ?>
        </span>
        <p style="margin:0; font-size:0.8rem; color:var(--text-muted); text-transform:uppercase;">Última Sincronização</p>
    </div>
</div>

<div class="card">
    <h2 style="font-size:1.2rem;">Histórico de Importações Auditadas</h2>
    <?php if (empty($imports)): ?>
        <p style="color:var(--text-muted); font-style:italic; text-transform:uppercase; font-size:0.85rem;">Nenhum registro de importação encontrado no banco de dados.</p>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:0.8rem; text-align:left; text-transform:uppercase;">
                <thead>
                    <tr style="border-bottom:2px solid var(--border-color); background:var(--bg-alt);">
                        <th style="padding:6px 8px;">ID</th>
                        <th style="padding:6px 8px;">Conjunto de Dados</th>
                        <th style="padding:6px 8px;">Arquivo / Hash</th>
                        <th style="padding:6px 8px;">Início</th>
                        <th style="padding:6px 8px;">Processados</th>
                        <th style="padding:6px 8px;">Inseridos</th>
                        <th style="padding:6px 8px;">Rejeitados</th>
                        <th style="padding:6px 8px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($imports as $imp): ?>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:6px 8px;">#<?= $imp['id'] ?></td>
                            <td style="padding:6px 8px; font-weight:700;"><?= $e($imp['dataset_name']) ?></td>
                            <td style="padding:6px 8px;">
                                <span style="font-family:monospace; font-size:0.7rem;"><?= $e(substr($imp['file_hash'], 0, 12)) ?>...</span>
                            </td>
                            <td style="padding:6px 8px;"><?= $e(date('d/m/Y H:i', strtotime($imp['started_at']))) ?></td>
                            <td style="padding:6px 8px;"><?= (int)$imp['records_processed'] ?></td>
                            <td style="padding:6px 8px; color:var(--status-deferido-text); font-weight:700;">+<?= (int)$imp['records_inserted'] ?></td>
                            <td style="padding:6px 8px; color:<?= $imp['records_rejected'] > 0 ? 'var(--status-indeferido-text)' : 'inherit' ?>;"><?= (int)$imp['records_rejected'] ?></td>
                            <td style="padding:6px 8px;">
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
