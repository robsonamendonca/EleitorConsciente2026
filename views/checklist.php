<?php
$pageTitle = 'Minha Cola Eleitoral 2026';
require __DIR__ . '/layout/header.php';
?>

<div style="margin-bottom: 1.5rem;" class="no-print">
    <h1>Minha Cola Eleitoral 2026</h1>
    <p style="color:var(--text-muted);">
        Organize seus candidatos para o dia da votação. Suas escolhas e anotações são salvas <strong>exclusivamente neste dispositivo (no seu navegador)</strong> e nunca são enviadas aos nossos servidores (Privacidade por Padrão e LGPD).
    </p>

    <div style="display:flex; gap:0.75rem; flex-wrap:wrap; margin-top:1rem;">
        <button id="btn-print-cola" class="btn btn-primary">
            🖨️ Imprimir Minha Cola
        </button>
        <button id="btn-clear-cola" class="btn btn-secondary">
            🗑️ Limpar Minha Cola
        </button>
    </div>
</div>

<div class="print-only" style="margin-bottom: 0.5rem; border-bottom: 2px solid #000; padding-bottom: 0.3rem;">
    <h1 style="font-size: 1.2rem; margin: 0;">Minha Cola Eleitoral - Eleições Gerais 2026</h1>
    <p style="font-size: 0.75rem; margin: 0; color: #444;">Estado de São Paulo / Eleição Federal | Impresso em <?= date('d/m/Y H:i') ?></p>
</div>

<div class="card" style="background:#fffbeb; border-color:#fef3c7; color:#92400e; margin-bottom:1.5rem;">
    <strong>⚠️ Lembrete Importante:</strong> Na cabine de votação, a ordem oficial de votação na urna eletrônica é: Deputado Federal, Deputado Estadual, Senador, Governador e Presidente. Sempre confira a foto e o nome do candidato no visor da urna antes de apertar a tecla CONFIRMA.
</div>

<div id="cola-page-container">
    <?php
    // Ordem recomendada oficial de votação na urna eletrônica
    // SENADOR permite 2 escolhas (cada estado elege 2 senadores)
    $votingOrder = ['DEPUTADO_FEDERAL', 'DEPUTADO_ESTADUAL', 'SENADOR_1', 'SENADOR_2', 'GOVERNADOR', 'PRESIDENTE'];
    $indexedOffices = [];
    foreach ($offices as $o) {
        $indexedOffices[$o['code']] = $o;
    }
    ?>

    <?php foreach ($votingOrder as $idx => $code): ?>
        <?php
        // Mapeia SENADOR_1/SENADOR_2 para o cargo real SENADOR
        $officeCode = preg_replace('/_\d+$/', '', $code);
        $off = $indexedOffices[$officeCode] ?? null;
        if (!$off) continue;
        $isSenatorSlot = str_starts_with($code, 'SENADOR_');
        $slotLabel = $isSenatorSlot ? ($code === 'SENADOR_1' ? '1º Senador' : '2º Senador') : $off['name'];
        ?>
        <div class="checklist-slot" data-office-code="<?= $e($code) ?>" data-base-office="<?= $e($officeCode) ?>" id="slot-<?= strtolower($code) ?>">
            <div class="checklist-slot-header">
                <div>
                    <span style="font-size:0.75rem; font-weight:800; color:var(--text-muted); text-transform:uppercase;">
                        <?= ($idx + 1) ?>º a votar na urna
                    </span>
                    <h3 style="margin:0;"><?= $e($slotLabel) ?></h3>
                </div>
            </div>

            <div class="slot-content">
                <!-- Preenchido dinamicamente pelo JavaScript -->
            </div>

            <div style="margin-top:0.75rem;">
                <label style="font-size:0.8rem; font-weight:600; color:var(--text-muted); display:block; margin-bottom:0.25rem;">
                    Minhas anotações pessoais (privadas):
                </label>
                <textarea class="slot-note checklist-notes" rows="1" placeholder="Anotações pessoais..."></textarea>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card no-print" style="margin-top:2rem; background-color:var(--bg-alt);">
    <h3>Sobre a privacidade da sua Cola</h3>
    <p style="font-size:0.9rem; color:var(--text-muted); margin:0;">
        O Eleitor Consciente 2026 segue as diretrizes da LGPD (Lei Geral de Proteção de Dados): não exigimos login, não coletamos nome, CPF ou título de eleitor, e nenhuma informação digitada acima trafega pela internet ou fica armazenada em bancos de dados remotos.
    </p>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>

