<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class ImportMunicipios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:municipios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa municípios do CSV para a tabela municípios';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = app_path('Console/Commands/municipios.csv');
        $columns = ['codigo_dados_abertos', 'nome'];

        $csv = Reader::createFromPath($file, 'r');
        $csv->setDelimiter(';'); // Set delimiter to semicolon
        $csv->setHeaderOffset(null); // No header
        $csv->appendStreamFilterOnRead('convert.iconv.ISO-8859-1/UTF-8');
        $records = $csv->getRecords();

        $data = [];
        foreach ($records as $record) {
            if (count($columns) == count($record)) {
                $row = array_combine($columns, $record);
                $data[] = $row;
            }
        }

        DB::table('municipios')->insert($data);

        $this->info('Municípios importados com sucesso!');
    }
}
