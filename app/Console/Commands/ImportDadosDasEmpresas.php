<?php

namespace App\Console\Commands;

use App\Models\Empresa;
use App\Models\Estabelecimento;
use App\Models\Socio;
use Illuminate\Console\Command;
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
        // TODO: checar falha desse no estabelecimento Y0 (59353055, 0001, 28, 1, DISK CALHAS E COBERTURAS METALICAS, 02, 20250207, 00, , 105, 20250207, 4120400, 7732201,4330499,4399199,2512800,2542000,2511000,8121400,8011101,8230001,9319101,7711000,7719501, RUA, RUA 5 CHACARA 117B LOTE, 57, , SETOR HABITACIONAL VICENTE PIRES, 72006185, DF, 9701, 61, 83011130, 0000, 00000000, , , DISK.CALHASERUFOS@GMAIL.COM, , , 2025-03-21 10:09:22, 2025-03-21 10:09:22) returning "id")
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
                        $this->insertOrUpdate($modelClass, $data);
                        $data = [];
                        $substringuedEndFileName = substr($file, -50);
                        $this->info("Inserted/Updated $counter records from file: ..." . $substringuedEndFileName);
                    }
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                    continue;
                }
            }

            if (!empty($data)) {
                $this->insertOrUpdate($modelClass, $data);
            }
        }
    }

    private function insertOrUpdate($modelClass, $data)
    {
        foreach ($data as $row) {
            try {
                $modelClass::updateOrCreate($row);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
                // encerra
                // exit(1);
                // wait 3 segs
                sleep(3);
                continue;
            }
            // algo mais?
        }
    }
}
