<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use App\Models\Estabelecimento;
use App\Models\Socio;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ImportDadosDasEmpresas extends Command
{
    protected $signature = 'import:dados';
    protected $description = 'Importa dados dos arquivos CSV para o banco de dados';

    public function handle()
    {
        $baseDir = $this->getDirDados();
        $this->importCsv(
            $baseDir . '/arquivos/descompactados/estabelecimentos',
            Estabelecimento::class
        );
        $this->importCsv(
            $baseDir . '/arquivos/descompactados/empresas',
            Empresa::class
        );
        $this->importCsv(
            $baseDir . '/arquivos/descompactados/socios',
            Socio::class
        );
    }

    private function getDirDados(): string
    {
        $dir = base_path(env('DADOS_ABERTOS_CNPJ_DIR', 'storage/app/dados_abertos_cnpj'));
        if (substr($dir, -1) === '/') {
            $dir = substr($dir, 0, -1);
        }
        return $dir;
    }

    private function importCsv($filesDir, $modelClass)
    {
        $files = glob($filesDir . '/*.CSV');
        $columns = (new $modelClass)->getFillable();

        foreach ($files as $file) {
            $csv = Reader::createFromPath($file, 'r');
            $csv->setDelimiter(';'); // Set delimiter to semicolon
            $csv->setHeaderOffset(null); // No header
            $csv->appendStreamFilterOnRead('convert.iconv.ISO-8859-1/UTF-8'); // Convert encoding to UTF-8

            $records = $csv->getRecords();
            $data = [];

            $counter = 0;
            foreach ($records as $record) {
                try {
                    if (count($columns) > count($record)) {
                        // então completa com null
                        $record = array_pad($record, count($columns), null);
                    } else if (count($columns) < count($record)) {
                        // então remove os excedentes
                        $record = array_slice($record, 0, count($columns));
                    }
                    $row = array_combine($columns, $record);
                    $data[] = $row;
                    if (count($data) >= 1000) {
                        $counter += count($data);
                        $this->insertOrUpdate($modelClass, $data, $filesDir);
                        $data = [];
                        $substringuedEndFileName = substr($file, -50);
                        $this->info("Inserted/Updated $counter records from file: ..." . $substringuedEndFileName);
                    }
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                    Log::error($e->getMessage());
                    $this->saveErrorData($row, $filesDir);
                    continue;
                }
            }

            if (!empty($data)) {
                $this->insertOrUpdate($modelClass, $data, $filesDir);
            }
        }
    }

    private function insertOrUpdate($modelClass, $data, $dir)
    {
        foreach ($data as $row) {
            try {
                $modelClass::updateOrCreate($row);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                $this->error($e->getMessage());

                // Salva os dados que deram erro em $filesDir/erros
                $this->saveErrorData($row, $dir);
                // encerra
                // exit(1);
                // wait 3 segs
                sleep(3);
                continue;
            }
            // algo mais?
        }
    }

    private function saveErrorData($row, $dir)
    {
        $errorDir = $dir . '/erros';
        if (!is_dir($errorDir)) {
            mkdir($errorDir, 0777, true);
        }
        $errorFile = $errorDir . '/erros.csv';
        // salva só os dados sem as colunas
        $row = array_values($row);
        $fp = fopen($errorFile, 'a');
        fputcsv($fp, $row);
        fclose($fp);
    }
}
