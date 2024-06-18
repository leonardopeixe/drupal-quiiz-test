#!/bin/bash

# Definir o UUID para evitar erros de Importação
drush config-get "system.site" uuid

# Definir a data atual
DATA_ATUAL=$(date +%Y-%m-%d_%H-%M-%S)

# Caminho de destino para a exportação (diretório acessível)
DESTINO="./config/$DATA_ATUAL"

# Criar diretório de destino
mkdir -p $DESTINO

# Exportar a configuração do Drupal
drush cex --destination=/app/$DESTINO/config -y

# Exportar o banco de dados
drush sql-dump --result-file=/app/$DESTINO/db-dump.sql --gzip

echo "Exportação concluída. Arquivos salvos em $DESTINO"
