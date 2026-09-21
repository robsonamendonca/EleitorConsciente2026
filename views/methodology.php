<?php
$pageTitle = 'Metodologia e Princípios - Eleitor Consciente 2026';
require __DIR__ . '/layout/header.php';
?>

<div style="margin-bottom: 2rem;">
    <h1>Metodologia e Princípios</h1>
    <p style="color:var(--text-muted); font-size:1.05rem;">
        Critérios éticos, tecnológicos e de neutralidade que regem o desenvolvimento e a operação do projeto.
    </p>
</div>

<div class="card">
    <h2>1. Neutralidade Informacional</h2>
    <p>
        O Eleitor Consciente tem o compromisso de <strong>informar, não persuadir</strong>. A plataforma não possui viés partidário ou ideológico, não elabora "notas de honestidade" ou "rankings de competência", e não utiliza algoritmos de recomendação ou persuasão de voto.
    </p>
</div>

<div class="card">
    <h2>2. Separação de Camadas</h2>
    <p>Para evitar confusões e desinformação, distinguimos rigorosamente:</p>
    <ul style="padding-left:1.5rem; line-height:1.8;">
        <li><strong>Dado Oficial:</strong> Registros homologados pela Justiça Eleitoral (TSE/TRE).</li>
        <li><strong>Declaração do Candidato:</strong> Planos de governo e propostas submetidas à Justiça Eleitoral.</li>
        <li><strong>Anotação Pessoal do Eleitor:</strong> Notas e avaliações registradas de forma privada no navegador do usuário.</li>
    </ul>
</div>

<div class="card">
    <h2>3. Auditabilidade e Não Invenção de Dados</h2>
    <p>
        Todo processamento é executado por rotinas abertas e verificáveis. Cada lote de importação tem seu hash criptográfico (SHA-256) registrado no banco de dados com contagem exata de registros inseridos, atualizados e rejeitados.
    </p>
</div>

<div class="card">
    <h2>4. Privacidade por Padrão (LGPD)</h2>
    <p>
        Não solicitamos login, senha, CPF ou dados biométricos. A funcionalidade da "Cola Eleitoral" opera unicamente em armazenamento local (localStorage) no dispositivo do eleitor.
    </p>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>

