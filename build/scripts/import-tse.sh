#!/bin/bash
# =============================================================================
# Eleitor Consciente 2026 - Importação de Dados do TSE
# Uso: ./import-tse.sh [sp|br|all]
# =============================================================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

FIXTURES_DIR="/var/www/html/database/fixtures"
OPTION="${1:-all}"

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Importação de Dados TSE               ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# Verificar se os CSVs existem
check_csv() {
    local file="$1"
    local label="$2"
    
    if [ ! -f "$FIXTURES_DIR/$file" ]; then
        echo -e "${RED}[ERRO] Arquivo não encontrado: $file${NC}"
        echo -e "  Baixe manualmente do TSE e coloque em:"
        echo -e "  ${YELLOW}$FIXTURES_DIR/$file${NC}"
        return 1
    fi
    
    LINES=$(wc -l < "$FIXTURES_DIR/$file")
    if [ "$LINES" -le 1 ]; then
        echo -e "${YELLOW}[AVISO] Arquivo $label está vazio (apenas cabeçalho)${NC}"
        return 1
    fi
    
    echo -e "${GREEN}[OK] $label: $((LINES - 1)) registros${NC}"
    return 0
}

case "$OPTION" in
    sp)
        echo "Verificando dados de São Paulo..."
        check_csv "tse_candidatos_sp_2026.csv" "SP" || exit 1
        ;;
    br)
        echo "Verificando dados Nacionais..."
        check_csv "tse_candidatos_br_2026.csv" "BR" || exit 1
        ;;
    all)
        echo "Verificando todos os arquivos..."
        SP_OK=false
        BR_OK=false
        
        check_csv "tse_candidatos_sp_2026.csv" "SP" && SP_OK=true
        check_csv "tse_candidatos_br_2026.csv" "BR" && BR_OK=true
        
        if ! $SP_OK && ! $BR_OK; then
            echo -e "${RED}[ERRO] Nenhum arquivo CSV encontrado!${NC}"
            echo ""
            echo "  Para baixar os dados do TSE:"
            echo "  1. Acesse: https://dadosabertos.tse.jus.br/dataset/candidatos-2026"
            echo "  2. Baixe: consulta_cand_2026_SP.csv"
            echo "  3. Baixe: consulta_cand_2026_BR.csv"
            echo "  4. Coloque em: $FIXTURES_DIR/"
            echo "  5. Renomeie para: tse_candidatos_sp_2026.csv e tse_candidatos_br_2026.csv"
            exit 1
        fi
        ;;
    *)
        echo "Uso: $0 [sp|br|all]"
        exit 1
        ;;
esac

echo ""
echo -e "${YELLOW}Executando importação...${NC}"

docker exec eleitor_php php importer/cli.php import-all

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Importação concluída!                 ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# Mostrar estatísticas
docker exec eleitor_php php importer/cli.php stats
