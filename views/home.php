<?php
$pageTitle = 'Eleitor Consciente 2026 - Início';
require __DIR__ . '/layout/header.php';
?>

<section class="hero">
    <h1>Informação eleitoral clara, neutra e auditável para 2026.</h1>
    <p>Consulte dados oficiais do TSE, compare propostas registradas e monte sua cola eleitoral com total privacidade no seu navegador.</p>
    <div style="margin-top: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="/candidatos?state_code=SP" class="btn btn-accent" style="font-size: 1.05rem; padding: 0.75rem 1.5rem;">Buscar Candidatos de SP</a>
        <a href="/candidatos?office=PRESIDENTE" class="btn btn-secondary" style="font-size: 1.05rem; padding: 0.75rem 1.5rem;">Ver Presidenciáveis</a>
        <a href="/cola-eleitoral" class="btn btn-secondary" style="font-size: 1.05rem; padding: 0.75rem 1.5rem;">Acessar Minha Cola</a>
    </div>
</section>

<div class="card" style="margin-bottom: 2rem;">
    <h2 style="font-size: 1.25rem; margin-bottom: 1rem;">Busca Rápida de Candidatos</h2>
    <form action="/candidatos" method="GET" class="filters-form">
        <div class="form-group" style="grid-column: span 2;">
            <label for="search-name">Nome ou número do candidato</label>
            <input type="text" id="search-name" name="name" class="form-control" placeholder="Ex: Ana, Marcos, 10, 1313...">
        </div>
        <div class="form-group">
            <label for="search-state">Abrangência</label>
            <select id="search-state" name="state_code" class="form-control">
                <option value="SP" selected>São Paulo (SP + Nacional)</option>
                <option value="BR">Apenas Nacional (Presidente)</option>
                <option value="ALL">Todos os estados</option>
            </select>
        </div>
        <div class="form-group">
            <label for="search-office">Cargo</label>
            <select id="search-office" name="office" class="form-control">
                <option value="">Todos os cargos</option>
                <?php foreach ($offices as $off): ?>
                    <option value="<?= $e($off['code']) ?>"><?= $e($off['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Pesquisar</button>
        </div>
    </form>
</div>

<section style="margin-bottom: 2.5rem;">
    <h2>Cargos em Disputa nas Eleições 2026</h2>
    <p style="color: var(--text-muted); font-size: 0.95rem;">Clique em um cargo para ver todos os candidatos cadastrados no TSE:</p>
    <div class="offices-grid">
        <?php foreach ($offices as $off): ?>
            <?php
            // Presidente: não filtrar por estado (usa ALL via WebController)
            $isNacional = isset($off['level']) && $off['level'] === 'FEDERAL';
            $officeLink = $isNacional
                ? '/candidatos?office=' . urlencode($off['code'])
                : '/candidatos?office=' . urlencode($off['code']) . '&state_code=SP';
            ?>
            <a href="<?= $officeLink ?>" class="office-card">
                <h3><?= $e($off['name']) ?></h3>
                <span><?= $e($isNacional ? '🇧🇷 Eleição Federal' : '📍 Eleição Estadual SP') ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<div class="card" style="background-color: #f8fafc; border-left: 4px solid var(--primary);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 style="margin-bottom: 0.25rem;">Transparência e Sincronização dos Dados</h3>
            <p style="margin: 0; font-size: 0.88rem; color: var(--text-muted);">
                Última sincronização com a base oficial: <strong><?= $e($lastUpdate) ?></strong>
            </p>
        </div>
        <div>
            <a href="/status" class="btn btn-sm btn-secondary">Ver Relatório de Auditoria</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>

