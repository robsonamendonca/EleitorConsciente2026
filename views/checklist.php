<?php
$pageTitle = 'Minha Cola Eleitoral 2026';
require __DIR__ . '/layout/header.php';
?>

<div style="margin-bottom: 1.25rem;" class="no-print">
    <h1>Minha Cola Eleitoral 2026</h1>
    <p style="color:var(--text-muted); text-transform:uppercase; font-size:0.85rem;">
        Organize seus candidatos para o dia da votação. Suas escolhas e anotações são salvas <strong>exclusivamente neste dispositivo (no seu navegador)</strong> e nunca são enviadas aos nossos servidores (Privacidade por Padrão e LGPD).
    </p>

    <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-top:0.75rem;">
        <button id="btn-print-cola" class="btn btn-primary">
            🖨️ Imprimir Minha Cola
        </button>
        <button id="btn-clear-cola" class="btn btn-corrige">
            🗑️ Limpar Minha Cola
        </button>
    </div>
</div>

<div class="print-only" style="margin-bottom: 0.4rem; border-bottom: 3px solid #000; padding-bottom: 0.2rem;">
    <h1 style="font-size: 1.1rem; margin: 0;">Minha Cola Eleitoral - Eleições Gerais 2026</h1>
    <p style="font-size: 0.7rem; margin: 0; color: #333; text-transform:uppercase;">Estado de São Paulo / Eleição Federal | Impresso em <?= date('d/m/Y H:i') ?></p>
</div>

<div class="card" style="background:#fffbeb; border-color:#F05A24; color:#856404; margin-bottom:1.25rem; border-left:6px solid #F05A24;">
    <strong style="text-transform:uppercase;">⚠ Lembrete Importante:</strong> <span style="text-transform:uppercase;">Na cabine de votação, a ordem oficial de votação na urna eletrônica é: Deputado Federal, Deputado Estadual, Senador, Governador e Presidente. Sempre confira a foto e o nome do candidato no visor da urna antes de apertar a tecla CONFIRMA.</span>
</div>

<div id="cola-page-container">
    <?php
    $votingOrder = ['DEPUTADO_FEDERAL', 'DEPUTADO_ESTADUAL', 'SENADOR_1', 'SENADOR_2', 'GOVERNADOR', 'PRESIDENTE'];
    $indexedOffices = [];
    foreach ($offices as $o) {
        $indexedOffices[$o['code']] = $o;
    }
    ?>

    <?php foreach ($votingOrder as $idx => $code): ?>
        <?php
        $officeCode = preg_replace('/_\d+$/', '', $code);
        $off = $indexedOffices[$officeCode] ?? null;
        if (!$off) continue;
        $isSenatorSlot = str_starts_with($code, 'SENADOR_');
        $slotLabel = $isSenatorSlot ? ($code === 'SENADOR_1' ? '1º Senador' : '2º Senador') : $off['name'];
        ?>
        <div class="checklist-slot" data-office-code="<?= $e($code) ?>" data-base-office="<?= $e($officeCode) ?>" id="slot-<?= strtolower($code) ?>">
            <div class="checklist-slot-header">
                <div>
                    <span style="font-size:0.7rem; font-weight:900; color:var(--text-light); text-transform:uppercase; letter-spacing:0.5px;">
                        <?= ($idx + 1) ?>º a votar na urna
                    </span>
                    <h3 style="margin:0; font-size:1.1rem;"><?= $e($slotLabel) ?></h3>
                </div>
            </div>

            <div class="slot-content">
                <!-- Preenchido dinamicamente pelo JavaScript -->
            </div>

            <div style="margin-top:0.5rem;">
                <label style="font-size:0.75rem; font-weight:700; color:var(--text-light); display:block; margin-bottom:0.2rem; text-transform:uppercase;">
                    Minhas anotações pessoais (privadas):
                </label>
                <textarea class="slot-note checklist-notes" rows="1" placeholder="Anotações pessoais..."></textarea>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Rodapé de Instruções Urna -->
<div class="urna-footer-instructions no-print" style="margin-top: 1.5rem;">
    <p>APERTE A TECLA:</p>
    <p><span class="verde">VERDE</span> PARA CONFIRMAR</p>
    <p><span class="laranja">LARANJA</span> PARA CORRIGIR</p>
</div>

<div class="card no-print" style="margin-top:1.5rem; background-color:#ffffff; border-left:6px solid var(--btn-teclas);">
    <h3 style="font-size:0.95rem; margin-bottom:0.3rem;">Sobre a privacidade da sua Cola</h3>
    <p style="font-size:0.8rem; color:var(--text-muted); margin:0; text-transform:uppercase;">
        O Eleitor Consciente 2026 segue as diretrizes da LGPD (Lei Geral de Proteção de Dados): não exigimos login, não coletamos nome, CPF ou título de eleitor, e nenhuma informação digitada acima trafega pela internet ou fica armazenada em bancos de dados remotos.
    </p>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
