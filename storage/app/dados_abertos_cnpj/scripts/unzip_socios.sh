#!/bin/bash
mkdir -p /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/socios
rm -f /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/socios/*
for f in /var/www/html/storage/app/dados_abertos_cnpj/arquivos/socios/*.zip; do
    tar -xf "$f" -C /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/socios
done
for f in /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/socios/*.SOCIOCSV; do
    mv "$f" "${f%.SOCIOCSV}.CSV"
done
