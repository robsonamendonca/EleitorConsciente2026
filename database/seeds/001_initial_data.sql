-- Dados iniciais (seeds) do Eleitor Consciente 2026

-- 1. Eleições
INSERT INTO elections (id, year, name, election_type, status)
VALUES (1, 2026, 'Eleições Gerais 2026', 'GERAL', 'ativo')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- 2. Cargos Oficiais
INSERT INTO offices (id, code, name, level) VALUES
(1, 'PRESIDENTE', 'Presidente da República', 'FEDERAL'),
(2, 'VICE_PRESIDENTE', 'Vice-Presidente da República', 'FEDERAL'),
(3, 'GOVERNADOR', 'Governador', 'ESTADUAL'),
(4, 'VICE_GOVERNADOR', 'Vice-Governador', 'ESTADUAL'),
(5, 'SENADOR', 'Senador', 'FEDERAL'),
(6, '1_SUPLENTE', '1º Suplente', 'FEDERAL'),
(7, '2_SUPLENTE', '2º Suplente', 'FEDERAL'),
(8, 'DEPUTADO_FEDERAL', 'Deputado Federal', 'FEDERAL'),
(9, 'DEPUTADO_ESTADUAL', 'Deputado Estadual', 'ESTADUAL')
ON DUPLICATE KEY UPDATE name=VALUES(name), level=VALUES(level);

-- 3. Partidos Políticos Oficiais do TSE
INSERT INTO parties (acronym, name, number) VALUES
('MDB', 'Movimento Democrático Brasileiro', '15'),
('PT', 'Partido dos Trabalhadores', '13'),
('PSDB', 'Partido da Social Democracia Brasileira', '45'),
('PP', 'Progressistas', '11'),
('PDT', 'Partido Democrático Trabalhista', '12'),
('UNIÃO', 'União Brasil', '44'),
('PL', 'Partido Liberal', '22'),
('PCdoB', 'Partido Comunista do Brasil', '65'),
('PSB', 'Partido Socialista Brasileiro', '40'),
('REPUBLICANOS', 'Republicanos', '10'),
('CIDADANIA', 'Cidadania', '23'),
('PODEMOS', 'Podemos', '20'),
('PSD', 'Partido Social Democrático', '55'),
('PV', 'Partido Verde', '43'),
('PSOL', 'Partido Socialismo e Liberdade', '50'),
('REDE', 'Rede Sustentabilidade', '18'),
('NOVO', 'Partido Novo', '30'),
('SOLIDARIEDADE', 'Solidariedade', '77'),
('PRD', 'Partido Renovação Democrática', '25'),
('DC', 'Democracia Cristã', '27'),
('PRTB', 'Partido Renovador Trabalhista Brasileiro', '28'),
('PMB', 'Partido da Mulher Brasileira', '35'),
('AGIR', 'Agir', '36'),
('AVANTE', 'Avante', '70'),
('PSTU', 'Partido Socialista dos Trabalhadores Unificado', '16'),
('PCB', 'Partido Comunista Brasileiro', '21'),
('PCO', 'Partido da Causa Operária', '29'),
('UP', 'Unidade Popular', '80')
ON DUPLICATE KEY UPDATE name=VALUES(name), number=VALUES(number);

-- 4. Fontes Oficiais
INSERT INTO sources (id, source_type, name, url, authority_level, active) VALUES
(1, 'TSE', 'Portal de Dados Abertos do TSE', 'https://dadosabertos.tse.jus.br/', 'OFICIAL', 1),
(2, 'TSE', 'DivulgaCandContas - Divulgação de Candidaturas e Contas Eleitorais', 'https://divulgacandcontas.tse.jus.br/', 'OFICIAL', 1),
(3, 'TRE', 'Tribunal Regional Eleitoral de São Paulo (TRE-SP)', 'https://www.tre-sp.jus.br/', 'OFICIAL', 1),
(4, 'CAMARA', 'Câmara dos Deputados - Dados Abertos', 'https://dadosabertos.camara.leg.br/', 'INSTITUCIONAL', 1),
(5, 'SENADO', 'Senado Federal - Dados Abertos', 'https://www12.senado.leg.br/dados-abertos', 'INSTITUCIONAL', 1)
ON DUPLICATE KEY UPDATE name=VALUES(name), url=VALUES(url);

