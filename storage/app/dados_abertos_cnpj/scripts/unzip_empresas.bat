@echo off
mkdir "C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/empresas"
del /q "C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/empresas\*"
for %%f in ("C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/empresas\*.zip") do (
  tar -xf "%%f" -C "C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/empresas"
)
for %%f in ("C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/empresas\*.EMPRECSV") do (
    ren "%%f" "%%~nf.CSV"
)
