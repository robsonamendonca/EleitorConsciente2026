<?php
$pageTitle = 'Candidatos - Eleições 2026';
require __DIR__ . '/../layout/header.php';
?>

<div style="margin-bottom: 1.5rem;">
    <h1>Consulta de Candidatos</h1>
    <p style="color: var(--text-muted);">
        Exibindo <?= count($candidates) ?> de <?= (int)$meta['total'] ?> candidatos encontrados.
    </p>
</div>

<div class="filters-bar">
    <form action="/candidatos" method="GET" class="filters-form">
        <div class="form-group">
            <label for="filtro-nome">Nome do candidato</label>
            <input type="text" id="filtro-nome" name="name" class="form-control" value="<?= $e($filters['name'] ?? '') ?>" placeholder="Busca por nome...">
        </div>
        <div class="form-group">
            <label for="filtro-numero">Número</label>
            <input type="text" id="filtro-numero" name="ballot_number" class="form-control" value="<?= $e($filters['ballot_number'] ?? '') ?>" placeholder="Ex: 10, 1313...">
        </div>
        <div class="form-group">
            <label for="filtro-cargo">Cargo</label>
            <select id="filtro-cargo" name="office" class="form-control">
                <option value="">Todos os cargos</option>
                <?php foreach ($offices as $off): ?>
                    <option value="<?= $e($off['code']) ?>" <?= ($filters['office'] === $off['code']) ? 'selected' : '' ?>>
                        <?= $e($off['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="filtro-abrangencia">Abrangência</label>
            <select id="filtro-abrangencia" name="state_code" class="form-control">
                <option value="SP" <?= (($filters['state_code'] ?? 'SP') === 'SP') ? 'selected' : '' ?>>Eleitor de SP (SP + Nacional)</option>
                <option value="BR" <?= (($filters['state_code'] ?? '') === 'BR') ? 'selected' : '' ?>>Apenas Nacional (Presidente)</option>
                <option value="ALL" <?= (($filters['state_code'] ?? '') === 'ALL') ? 'selected' : '' ?>>Todos os estados</option>
            </select>
        </div>
        <div class="form-group">
            <label for="filtro-partido">Partido</label>
            <select id="filtro-partido" name="party" class="form-control">
                <option value="">Todos os partidos</option>
                <?php foreach ($parties as $pt): ?>
                    <option value="<?= $e($pt['acronym']) ?>" <?= ($filters['party'] === $pt['acronym']) ? 'selected' : '' ?>>
                        <?= $e($pt['acronym']) ?> - <?= $e($pt['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="filtro-ordem">Ordenar por</label>
            <select id="filtro-ordem" name="sort" class="form-control">
                <option value="ballot_name" <?= ($filters['sort'] === 'ballot_name') ? 'selected' : '' ?>>Nome de urna (A-Z)</option>
                <option value="ballot_number" <?= ($filters['sort'] === 'ballot_number') ? 'selected' : '' ?>>Número da urna</option>
                <option value="party_acronym" <?= ($filters['sort'] === 'party_acronym') ? 'selected' : '' ?>>Partido</option>
            </select>
        </div>
        <div style="display:flex; gap:0.5rem;">
            <button type="submit" class="btn btn-primary" style="flex:1;">Filtrar</button>
            <a href="/candidatos" class="btn btn-secondary">Limpar</a>
        </div>
    </form>
</div>

<?php if (empty($candidates)): ?>
    <div class="card" style="text-align: center; padding: 3rem 1.5rem;">
        <h3 style="color: var(--text-muted); margin-bottom: 0.5rem;">Nenhum candidato encontrado</h3>
        <p style="color: var(--text-light); max-width: 500px; margin: 0 auto 1.5rem auto;">
            Tente ajustar os filtros de busca ou remover os termos pesquisados.
        </p>
        <a href="/candidatos" class="btn btn-primary">Ver todos os candidatos</a>
    </div>
<?php else: ?>
    <div class="candidates-grid">
        <?php foreach ($candidates as $cand): ?>
            <?php
            $isNacional = ($cand['state_code'] === 'BR');
            $scopeLabel = $isNacional ? '🇧🇷 Nacional' : '📍 Estadual';
            $scopeColor = $isNacional ? 'var(--primary)' : 'var(--secondary)';
            ?>
            <div class="candidate-card">
                <div>
                    <div class="candidate-header">
                        <img src="<?= $e($cand['photo_url'] ?: '/assets/images/placeholder.svg') ?>"
                             alt="Foto de <?= $e($cand['ballot_name']) ?>"
                             class="candidate-photo"
                             loading="lazy"
                             onerror="this.src='/assets/images/placeholder.svg'">
                        <div class="candidate-info">
                            <h3 class="candidate-ballot-name"><?= $e($cand['ballot_name']) ?></h3>
                            <div class="candidate-full-name"><?= $e($cand['full_name']) ?></div>
                            <span class="candidate-number-badge"><?= $e($cand['ballot_number']) ?></span>
                        </div>
                    </div>

                    <div class="candidate-meta">
                        <p>
                            <strong>Cargo:</strong> <?= $e($cand['office_name']) ?>
                            <span style="
                                display: inline-block;
                                margin-left: 0.4rem;
                                padding: 0.1rem 0.45rem;
                                border-radius: 999px;
                                font-size: 0.7rem;
                                font-weight: 600;
                                color: #fff;
                                background: <?= $scopeColor ?>;
                                vertical-align: middle;
                            "><?= $scopeLabel ?></span>
                        </p>
                        <p><strong>Partido:</strong> <?= $e($cand['party_acronym']) ?><?= !empty($cand['federation_name']) ? ' (' . $e($cand['federation_name']) . ')' : '' ?></p>
                        <p>
                            <strong>Situação TSE:</strong>
                            <span class="status-badge <?= (stripos($cand['registration_status'], 'deferido') !== false) ? 'status-deferido' : 'status-pendente' ?>">
                                <?= $e($cand['registration_status']) ?>
                            </span>
                        </p>
                        <p style="font-size: 0.78rem; color: var(--text-light); margin-top: 0.4rem;">
                            Fonte oficial TSE | Atualizado em: <?= $e(date('d/m/Y', strtotime($cand['source_last_updated_at'] ?? $cand['updated_at']))) ?>
                        </p>
                    </div>
                </div>

                <div style="display: flex; gap: 0.5rem; margin-top: 1rem; border-top: 1px solid var(--border-subtle); padding-top: 0.75rem;">
                    <a href="/candidato/<?= $cand['id'] ?>" class="btn btn-sm btn-secondary" style="flex:1;">Ver Detalhes</a>
                    <button class="btn btn-sm btn-accent btn-add-cola"
                            data-id="<?= $cand['id'] ?>"
                            data-office-code="<?= $e($cand['office_code']) ?>"
                            data-office-name="<?= $e($cand['office_name']) ?>"
                            data-number="<?= $e($cand['ballot_number']) ?>"
                            data-name="<?= $e($cand['ballot_name']) ?>"
                            data-party="<?= $e($cand['party_acronym']) ?>"
                            data-photo="<?= $e($cand['photo_url'] ?? '') ?>">
                        + Na Cola
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Paginação -->
    <?php if ($meta['total_pages'] > 1): ?>
        <nav class="pagination" aria-label="Paginação de candidatos">
            <?php for ($p = 1; $p <= $meta['total_pages']; $p++): ?>
                <?php
                $queryParams = $filters;
                $queryParams['page'] = $p;
                $queryUrl = '/candidatos?' . http_build_query($queryParams);
                ?>
                <?php if ($p == $meta['page']): ?>
                    <span class="active" aria-current="page"><?= $p ?></span>
                <?php else: ?>
                    <a href="<?= $e($queryUrl) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
