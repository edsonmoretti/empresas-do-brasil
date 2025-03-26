@echo off
mkdir "C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/socios"
del /q "C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/socios\*"
for %%f in ("C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/socios\*.zip") do (
  tar -xf "%%f" -C "C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/socios"
)
for %%f in ("C:\SourceCodes\dados-das-empresas\storage/app/dados_abertos_cnpj/arquivos/descompactados/socios\*.SOCIOCSV") do (
    ren "%%f" "%%~nf.CSV"
)
