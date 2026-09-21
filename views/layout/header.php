<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($pageTitle ?? 'Eleitor Consciente 2026 - Informação Eleitoral Neutra e Auditável') ?></title>
    <meta name="description" content="Plataforma pública e independente de consulta a candidatos das Eleições Gerais de 2026 com base em dados oficiais do TSE.">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="manifest" href="/manifest.webmanifest">
    <meta name="theme-color" content="#1a1a1a">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a href="/" class="brand-link">
                <span class="brand-badge">2026</span> Eleitor Consciente
            </a>
            <nav class="main-nav" aria-label="Navegação Principal">
                <a href="/">Início</a>
                <a href="/candidatos">Candidatos</a>
                <a href="/cola-eleitoral" class="nav-cola-btn">Minha Cola<span id="nav-cola-count"></span></a>
                <a href="/fontes">Fontes</a>
                <a href="/metodologia">Metodologia</a>
                <a href="/status">Status</a>
            </nav>
        </div>
    </header>

    <div class="neutrality-banner">
        <div class="container">
            <strong>⚠ Compromisso de Neutralidade:</strong> Esta plataforma não recomenda candidatos, não realiza rankings políticos e não emite juízo de valor. Todos os dados são extraídos de fontes públicas oficiais (TSE/TRE). A decisão de voto é exclusiva do eleitor.
        </div>
    </div>

    <div id="live-feedback" style="display:none; position:fixed; bottom:20px; right:20px; background:#00A859; color:#ffffff; padding:12px 20px; border-radius:4px; box-shadow:0 4px 12px rgba(0,0,0,0.25); z-index:9999; font-weight:900; text-transform:uppercase; font-family:'Arial Black',Arial,sans-serif;"></div>

    <main class="main-content">
        <div class="container">
