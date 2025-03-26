@echo off
mkdir "C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/estabelecimentos"
del /q "C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/estabelecimentos\*"
for %%f in ("C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/estabelecimentos\*.zip") do (
  tar -xf "%%f" -C "C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/estabelecimentos"
)
for %%f in ("C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/estabelecimentos\*.ESTABELE") do (
    ren "%%f" "%%~nf.CSV"
)
