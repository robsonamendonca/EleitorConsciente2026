<?php
$pageTitle = 'Sobre o Projeto - Eleitor Consciente 2026';
require __DIR__ . '/layout/header.php';
?>

<div style="margin-bottom: 1.5rem;">
    <h1>Sobre o Eleitor Consciente 2026</h1>
    <p style="color:var(--text-muted); text-transform:uppercase; font-size:0.85rem;">
        Uma iniciativa aberta de cidadania digital e transparência pública para as Eleições Gerais de 2026.
    </p>
</div>

<div class="card">
    <h2 style="font-size:1.2rem;">Objetivo</h2>
    <p style="text-transform:uppercase; font-size:0.9rem;">
        Facilitar o acesso do cidadão às informações públicas de candidaturas homologadas pelo TSE, com especial foco inicial no estado de São Paulo, permitindo a consulta ágil de números de urna, situação cadastral, propostas e fontes governamentais.
    </p>
</div>

<div class="card">
    <h2 style="font-size:1.2rem;">Open Source e Licença</h2>
    <p style="text-transform:uppercase; font-size:0.9rem;">
        O código-fonte deste projeto é distribuído sob a <strong>Licença MIT</strong>, incentivando a colaboração de desenvolvedores, pesquisadores e organizações da sociedade civil.
    </p>
    <p style="text-transform:uppercase; font-size:0.9rem;">
        Repositório oficial no GitHub: <a href="https://github.com/robsonamendonca/EleitorConsciente2026" target="_blank" rel="noopener noreferrer">https://github.com/robsonamendonca/EleitorConsciente2026</a>
    </p>
</div>

<div class="card">
    <h2 style="font-size:1.2rem;">Stack Tecnológica</h2>
    <ul style="padding-left:1.5rem; line-height:1.8; text-transform:uppercase; font-size:0.85rem;">
        <li><strong>Backend:</strong> PHP 8.2 (Vanilla / Modular PSR-4 com PDO e MySQL 8.0)</li>
        <li><strong>Frontend:</strong> HTML5 semântico, CSS3 responsivo e JavaScript Vanilla (sem dependências pesadas)</li>
        <li><strong>Infraestrutura:</strong> Docker e Docker Compose turnkey com suporte a hospedagem compartilhada</li>
    </ul>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
