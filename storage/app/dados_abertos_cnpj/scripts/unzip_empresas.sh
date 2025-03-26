#!/bin/bash
mkdir -p /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/empresas
rm -f /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/empresas/*
for f in /var/www/html/storage/app/dados_abertos_cnpj/arquivos/empresas/*.zip; do
    unzip "$f" -d /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/empresas
done
for f in /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/empresas/*.EMPRECSV; do
    mv "$f" "${f%.EMPRECSV}.CSV"
done
