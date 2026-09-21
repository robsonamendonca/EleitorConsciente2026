# Build - Eleitor Consciente 2026

Pasta contendo todos os artefatos necessários para publicação em hospedagem, VPS ou cloud.

## Estrutura

```
build/
├── README.md                    # Este arquivo
├── eleitor_consciente_dump.sql  # Dump completo do banco (schema + dados)
├── .env.production              # Template de variáveis de ambiente
├── docker-compose.yml           # Docker Compose para produção
├── Dockerfile                   # Dockerfile para produção
├── docker-entrypoint.sh         # Script de inicialização do container
├── docker/
│   ├── php/
│   │   └── php.ini             # Configurações PHP para produção
│   ├── mysql/
│   │   └── my.cnf              # Configurações MySQL para produção
│   └── apache/
│       └── vhost.conf          # Virtual Host Apache
├── nginx/
│   └── default.conf            # Configuração Nginx (alternativa)
└── scripts/
    ├── deploy.sh               # Script principal de deploy
    ├── setup-vps.sh            # Setup inicial VPS (Ubuntu/Debian)
    ├── backup.sh               # Backup automático do banco
    ├── import-tse.sh           # Importação de dados do TSE
    └── cron-setup.sh           # Configuração de crons
```

## Quick Start (VPS/Cloud)

```bash
# 1. Acessar o VPS
ssh usuario@seu-servidor

# 2. Clonar o repositório
git clone https://github.com/robsonamendonca/EleitorConsciente2026.git
cd EleitorConsciente2026

# 3. Executar setup inicial
chmod +x build/scripts/setup-vps.sh
sudo ./build/scripts/setup-vps.sh

# 4. Configurar variáveis de ambiente
cp build/.env.production .env
nano .env  # Editar senhas e domínio

# 5. Executar deploy
chmod +x build/scripts/deploy.sh
./build/scripts/deploy.sh
```

## Docker (Produção)

```bash
# Copiar variáveis de ambiente
cp build/.env.production .env

# Editar .env com suas credenciais
nano .env

# Subir em modo production
docker compose -f build/docker-compose.yml up -d --build

# Importar dump do banco
docker exec -i eleitor_mysql mysql -uroot -pSENHA eleitor_consciente < build/eleitor_consciente_dump.sql

# Criar symlinks das fotos
docker exec eleitor_php bash /var/www/html/docker-entrypoint.sh
```

## Nginx (Alternativa ao Apache)

```bash
# Copiar configuração
sudo cp build/nginx/default.conf /etc/nginx/sites-available/eleitor-consciente
sudo ln -s /etc/nginx/sites-available/eleitor-consciente /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

## Backup Automático

O script `scripts/backup.sh` deve ser configurado via cron:

```bash
# Backup diário às 3h da manhã
0 3 * * * /var/www/html/build/scripts/backup.sh >> /var/log/eleitor-backup.log 2>&1
```

## Importação de Dados TSE

Para atualizar os dados dos candidatos:

```bash
# Baixar CSVs do TSE (manualmente via navegador)
# Colocar em database/fixtures/tse_candidatos_sp_2026.csv
# Colocar em database/fixtures/tse_candidatos_br_2026.csv

# Executar importação
docker exec eleitor_php php importer/cli.php import-all
```
