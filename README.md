- [Doc Quiz Criar](quiz-criar.pdf)

- [Doc Quiz Responder - Logado](quiz-fazer-logado.pdf)

- [Doc Quiz Responder - Deslogado](quiz-fazer.pdf)

- [Doc Quiz Ver Resultado](quiz-resultados.pdf)

## PARA O TESTE
Execute os passos abaixo de instalação, esse é um projeto da minha autoria com algumas modificações para facilitar o desenvolvimento com drupal.

### PARA IMPORTAR O TESTE CORRETAMENTE APÓS A INSTALAÇÃO EXECUTE:
Certifique-se de que os arquivos de script estão usando o formato de final de linha (EOL) LF (Line Feed)
```bash
lando start
```
```bash
lando init-drupal
```
```bash
lando import "/app/quiz-test"
```
** Para o init-drupal e import funcionar precisa confirmar se o arquivo no script esta em LF **

## MELHORIAS
- Carregar em libraries os dados do quiz-maker-results.twig (não feito por falta de tempo)
- Carregar traduções e finalizar a configurações como alteração de Quiz para Votação.
- Melhorar o feedback para o usuários
- API ter mais opções de filtros para o consumo

## Exemplo JSON Quiz
```bash
[
    {
        "quiz_id": 4,
        "label": "Result of \"Teste Voto 1\"",
        "total_responses": 8,
        "responses": {
            "Teste Pergunta 1.1": {
                "label": "Teste Pergunta 1.1",
                "content": "<p>Teste Pergunta 1</p>",
                "value": {
                    "Teste Resposta 1": 3,
                    "Teste Resposta 2": 3
                }
            }
        }
    },
    {
        "quiz_id": 5,
        "label": "Result of \"Votação sobre batata\"",
        "total_responses": 5,
        "responses": {
            "Batata é sua comida favorita?": {
                "label": "Batata é sua comida favorita?",
                "content": "<p>Com base no seu gosto batata é sua preferida?</p>",
                "value": {
                    "Não, prefiro muito mais bacon": 1,
                    "Não, mas com arroz gosto": 1
                }
            },
            "Você prefere batata com:": {
                "label": "Você prefere batata com:",
                "content": "<p>Você prefere batata com:</p>",
                "value": {
                    "Arroz e Feijão": 2,
                    "Xuxu e Abacate": 1
                }
            }
        }
    }
]
```
# Draoi-Drupal Project

Este projeto utiliza Lando para facilitar o desenvolvimento e a gestão do ambiente Drupal. Abaixo estão os requisitos, comandos de configuração inicial e instruções para importar e exportar configurações e bancos de dados.

## Configuração Inicial

### Requisitos

Certifique-se de ter as seguintes versões instaladas:
1. **Docker**: 26.1.4
2. **Docker Compose**: 2.27.0
3. **Lando**: v3.21.0

### Iniciar o Projeto

Para iniciar o ambiente de desenvolvimento, execute o comando:
```bash
lando start
```

### Verificação de Arquivos de Script

Certifique-se de que os arquivos de script estão usando o formato de final de linha (EOL) LF (Line Feed).

### Inicializar Drupal com Lando

Para configurar e iniciar um novo projeto Drupal automaticamente com todas as configurações necessárias, use o comando:
```bash
lando init-drupal
```

#### Configuração Manual Alternativa

Se preferir configurar manualmente, siga os passos abaixo:

### Dependências do Composer

Durante a instalação, o Composer pode expirar em algumas máquinas. Para aumentar o tempo limite do Composer, execute:
```bash
lando composer config --global process-timeout 2000
```
Em seguida, instale as dependências do Composer:
```bash
lando composer install
```

## Importação e Exportação de Configurações e Banco de Dados

### Importar Configurações e Banco de Dados

Para importar as configurações do Drupal e um dump SQL, utilize:
```bash
lando drush site:install --db-url=mysql://drupal10:drupal10@database/drupal10 -y
```
Depois, importe as configurações e o banco de dados:
```bash
lando import
```

### Escolher Configuração para Importação

Você pode especificar um caminho para a configuração que deseja importar:
```bash
lando import "[CAMINHO PARA A CONFIGURAÇÃO]"
```


### Exportar Configurações e Banco de Dados

Para exportar as configurações do Drupal e um dump SQL, utilize:
```bash
lando export
```

## Informações Adicionais

### Acessar admin
```bash
lando drush uli --uri="https://draoi-drupal.lndo.site"
```

### Para listar informações sobre esta aplicação Lando, execute:
```bash
lando info
```
