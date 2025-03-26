#!/bin/bash
mkdir -p /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/estabelecimentos
rm -f /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/estabelecimentos/*
for f in /var/www/html/storage/app/dados_abertos_cnpj/arquivos/estabelecimentos/*.zip; do
    unzip "$f" -d /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/estabelecimentos
done
for f in /var/www/html/storage/app/dados_abertos_cnpj/arquivos/descompactados/estabelecimentos/*.ESTABELE; do
    mv "$f" "${f%.ESTABELE}.CSV"
done
