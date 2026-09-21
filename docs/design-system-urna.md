# Design System: Sistema Web Urna Eletrônica

Este documento estabelece as especificações de design e estilo para o desenvolvimento de sistemas web baseados na identidade visual da **Urna Eletrônica Brasileira**. O objetivo é garantir **contraste máximo**, **acessibilidade plena** e a **estética oficial** do Terminal do Eleitor.

---

## 🎨 Paleta de Cores (Design Tokens)

As cores do sistema seguem uma lógica estritamente utilitária, priorizando alto contraste para garantir a legibilidade em qualquer tipo de monitor ou tela.

| Elemento UI | Cor Hexadecimal | Aplicação em CSS |
| :--- | :--- | :--- |
| **Fundo da Tela** | `#FFFFFF` (Branco Puro) | `background-color: #ffffff;` |
| **Fundo de Blocos** | `#F4F4F4` (Cinza Claro) | `background-color: #f4f4f4;` |
| **Texto Principal** | `#000000` (Preto Puro) | `color: #000000;` |
| **Borda / Divisores**| `#333333` (Cinza Escuro) | `border-color: #333333;` |
| **Tecla Corrige** | `#F05A24` (Laranja) | `background-color: #f05a24;` |
| **Tecla Confirma** | `#00A859` (Verde Urna) | `background-color: #00a859;` |
| **Tecla Branco** | `#FFFFFF` (Borda Preta) | `background-color: #ffffff; border: 2px solid #000;` |
| **Teclas Numéricas** | `#1A1A1A` (Preto Urna) | `background-color: #1a1a1a; color: #fff;` |

---

## 🔤 Tipografia e Hierarquia Visual

O sistema utiliza fontes robustas, sem serifa (*sans-serif*), garantindo leitura rápida mesmo para usuários com baixa acuidade visual.

*   **Família Tipográfica Principal:** `Arial`, `Helvetica` ou `sans-serif` (em peso *Bold* / *Black*).
*   **Caixa do Texto:** Obrigatoriamente **CAIXA ALTA (UPPERCASE)** para cargos, nomes de candidatos e mensagens de instrução de rodapé.
*   **Tamanhos de Fonte Recomendados:**
    *   Título do Cargo: `32px` (Bold)
    *   Números Digitados: `48px` (Dentro dos blocos correspondentes)
    *   Nome do Candidato / Partido: `20px` (Bold)
    *   Mensagens de Rodapé: `18px` (Regular)

---

## 📐 Estrutura e Componentes da Interface (UI)

### 1. Grid dos Campos Numéricos
Os quadrados que recebem os números devem ser centralizados e possuir dimensões fixas com bordas pretas espessas.
*   **Tamanho do bloco:** `60px` de largura por `80px` de altura.
*   **Estilo CSS:** `border: 3px solid #000000; font-size: 48px; text-align: center; font-weight: bold;`

### 2. Tela de Confirmação (Card do Candidato)
Quando o número estiver completo, a tela exibe os dados do candidato respeitando as seguintes posições:
*   **Lado Esquerdo:** Nome do cargo, número digitado, nome do candidato e nome do partido político (tudo alinhado à esquerda).
*   **Lado Direito:** Foto do candidato em formato retangular vertical (`proporção 3:4`), com moldura preta fina.

### 3. Rodapé de Instruções
Área separada por uma linha horizontal preta espessa (`border-top: 2px solid #000;`), exibindo o texto de ajuda piscando ou estático:
*   "APERTE A TECLA:"
*   "**VERDE** PARA CONFIRMAR"
*   "**LARANJA** PARA CORRIGIR"

---

## 💻 Exemplo de Implementação (CSS Classes)

Para aplicar o padrão da urna em seu projeto web, utilize o guia de classes utilitárias abaixo:

```css
/* Reset e Fundo Oficial */
.urna-body {
    background-color: #ffffff;
    color: #000000;
    font-family: 'Arial Black', Arial, sans-serif;
    text-transform: uppercase;
}

/* Quadrado de Dígito */
.urna-digito-box {
    width: 60px;
    height: 80px;
    border: 3px solid #000000;
    font-size: 48px;
    line-height: 80px;
    text-align: center;
    display: inline-block;
    margin-right: 8px;
}

/* Botões do Teclado Virtual (Se houver) */
.btn-urna {
    font-family: Arial, sans-serif;
    font-weight: bold;
    border-radius: 4px;
    text-transform: uppercase;
    cursor: pointer;
}

.btn-urna-branco {
    background-color: #ffffff;
    color: #000000;
    border: 2px solid #000000;
}

.btn-urna-corrige {
    background-color: #f05a24;
    color: #000000;
    border: none;
}

.btn-urna-confirma {
    background-color: #00a859;
    color: #000000;
    border: none;
    padding: 15px 30px; /* Tamanho maior proporcional */
}
```
