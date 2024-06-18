#!/bin/bash

# Função para encontrar a última exportação (baseada na data mais recente)
encontrar_ultima_exportacao() {
    ls -d /app/config/*/ | sort -r | head -n 1
}

# Caminho de origem para a importação
# Se um argumento for passado, use-o como o caminho da configuração; caso contrário, encontre a última exportação
ORIGEM=${1:-$(encontrar_ultima_exportacao)}

if [ -d "$ORIGEM" ]; then
    echo "Importando a partir de /$ORIGEM"

    # Importar a configuração do Drupal
    drush cim --source=/$ORIGEM/config -y

    # Restaurar o banco de dados
    gunzip < /$ORIGEM/db-dump.sql.gz | drush sql-cli

    # Atualizar banco de dados e limpar cache
    drush updb -y
    drush cr

    echo "Importação concluída com sucesso."
else
    echo "Diretório de origem não encontrado: $ORIGEM"
    exit 1
fi
