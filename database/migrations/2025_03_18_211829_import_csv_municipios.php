<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use League\Csv\Reader;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // importa municipios do csv em storage/app/dados_abertos_cnpj/municipios.csv na tabela municipios
        // sendo codigo_dados_abertos = codigo no csv, nome = nome no csv, uf = null, codigo_ibge = null
        $file = storage_path('app/dados_abertos_cnpj/municipios.csv');
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('municipios')->truncate();
    }
};
