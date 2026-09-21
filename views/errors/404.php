<?php
$pageTitle = 'Página Não Encontrada (404) - Eleitor Consciente 2026';
require __DIR__ . '/../layout/header.php';
?>

<div class="card" style="text-align: center; padding: 4rem 1.5rem; max-width: 600px; margin: 2rem auto;">
    <h1 style="font-size: 3rem; color: var(--primary); margin-bottom: 0.5rem;">404</h1>
    <h2>Página Não Encontrada</h2>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">
        <?= $e($message ?? 'O endereço solicitado não existe ou o candidato não foi localizado em nossa base.') ?>
    </p>
    <a href="/candidatos" class="btn btn-primary">Voltar para a Lista de Candidatos</a>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>

