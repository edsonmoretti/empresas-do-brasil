<?php

namespace App\Console\Commands;

use DateTime;
use DOMDocument;
use DOMXPath;
use Illuminate\Console\Command;

class ScriptsEmpresas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:scripts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa o script de geração de arquivos para empresas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->init();
    }

    // Função para obter a URL do diretório mais recente
    private function getMostRecentURL(): string
    {
        $url = env('DADOS_ABERTOS_CNPJ_URL');

        // Obtém o conteúdo da página
        $html = file_get_contents($url);

        // Cria um novo objeto DOMDocument
        $dom = new DOMDocument();

        // Carrega o HTML na estrutura DOM
        @$dom->loadHTML($html);

        // Cria um novo objeto DOMXPath
        $xpath = new DOMXPath($dom);

        // Consulta todos os links de diretórios
        $nodes = $xpath->query('//tr/td/a');

        // Array para armazenar os diretórios e suas datas
        $directories = [];

        // Itera sobre os nós encontrados
        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');
            if (preg_match('/\d{4}-\d{2}\//', $href)) {
                // Adiciona o diretório ao array
                $directories[] = rtrim($href, '/');
            }
        }

        // Ordena os diretórios pela data
        usort($directories, function ($a, $b) {
            $dateA = DateTime::createFromFormat('Y-m', $a);
            $dateB = DateTime::createFromFormat('Y-m', $b);
            return $dateB <=> $dateA;
        });

        // Obtém o diretório mais recente
        $mostRecentDirectory = $directories[0];

        // Exibe a URL completa do diretório mais recente
        // if url end with /, remove it
        if (str_ends_with($url, '/')) {
            $url = substr($url, 0, -1);
        }
        $mostRecentUrl = $url . '/' . $mostRecentDirectory . '/';
        echo "URL Configurada: \n".$mostRecentUrl . "\n\n";
        return $mostRecentUrl;
    }

    // Função para obter os arquivos de um diretório específico
    private function getFilesFromDirectory($url, $prefix): array
    {
        // Obtém o conteúdo da página do diretório
        $html = file_get_contents($url);

        // Cria um novo objeto DOMDocument
        $dom = new DOMDocument();

        // Carrega o HTML na estrutura DOM
        @$dom->loadHTML($html);

        // Cria um novo objeto DOMXPath
        $xpath = new DOMXPath($dom);

        // Consulta todos os links de arquivos
        $nodes = $xpath->query('//tr/td/a');

        // Array para armazenar os arquivos que começam com o prefixo especificado
        $files = [];

        // Itera sobre os nós encontrados
        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');
            if (strpos($href, $prefix) === 0) {
                // Adiciona o arquivo ao array
                $files[] = $url . $href;
            }
        }

        return $files;
    }

    private function getDirDados(): string
    {
        $dir = base_path(env('DADOS_ABERTOS_CNPJ_DIR', 'storage/app/dados_abertos_cnpj'));
        // se string termina com / remove
        if (substr($dir, -1) === '/') {
            $dir = substr($dir, 0, -1);
        }
        return $dir;
    }

    // Função para gerar o script de download
    private function generateDownloadScript($files, $name): void
    {
        $dirDados = $this->getDirDados();
        $directory = $dirDados . '/arquivos/' . $name;

        // Cria o conteúdo do arquivo .bat (se for windows)
        if (getenv('OS') === 'Windows_NT') {
            $ext = 'bat';
            $batShContent = "@echo off\n";
            $batShContent .= "mkdir \"$directory\"\n";
            foreach ($files as $file) {
                $batShContent .= "curl -o \"$directory\\" . basename($file) . "\" " . $file . "\n";
            }
        } else {
            $ext = 'sh';
            $batShContent = "#!/bin/bash\n";
            $batShContent .= "mkdir -p \"$directory\"\n";
            foreach ($files as $file) {
                $batShContent .= "curl -o \"$directory/" . basename($file) . "\" " . $file . "\n";
            }
        }

        // Cria diretórios recursivamente, se necessário
        if (!file_exists($dirDados . '/scripts')) {
            mkdir($dirDados . '/scripts', 0777, true);
        }
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $scriptFile = "$dirDados/scripts/download_$name.$ext";
        file_put_contents($scriptFile, $batShContent);
        echo "Script $scriptFile gerado com sucesso!\n";
    }

    // Função para gerar o script de descompactação
    private function generateUnzipScript($name, $extension): void
    {
        $ditPath = $this->getDirDados();
        $directoryOrigin = $ditPath . '/arquivos/' . $name;
        $directoryDestiny = $ditPath . '/arquivos/' . 'descompactados/' . $name;

        // Cria o conteúdo do arquivo .bat (se for windows)
        if (getenv('OS') === 'Windows_NT') {
            $ext = 'bat';
            $batShContent = "@echo off\n";
            // Cria o diretório de destino
            $batShContent .= "mkdir \"$directoryDestiny\"\n";

            // Limpa o diretório de destino
            $batShContent .= "del /q \"$directoryDestiny\\*\"\n";

            // Descompacta os arquivos .zip
            $batShContent .= "for %%f in (\"$directoryOrigin\\*.zip\") do (\n";
            $batShContent .= "  tar -xf \"%%f\" -C \"$directoryDestiny\"\n";
            $batShContent .= ")\n";

            // Renomeia os arquivos com a extensão especificada
            $batShContent .= "for %%f in (\"$directoryDestiny\\*.$extension\") do (\n";
            $batShContent .= "    ren \"%%f\" \"%%~nf.CSV\"\n";
            $batShContent .= ")\n";
        } else {
            $ext = 'sh';
            $f = '$f';
            $batShContent = "#!/bin/bash\n";

            // Cria o diretório de destino
            $batShContent .= "mkdir -p \"$directoryDestiny\"\n";

            // Limpa o diretório de destino
            $batShContent .= "rm -f \"$directoryDestiny/*\"\n";

            // Descompacta os arquivos .zip
            $batShContent .= "for f in \"$directoryOrigin/*.zip\"; do\n";
            $batShContent .= "    tar -xf \"$f\" -C \"$directoryDestiny\"\n";
            $batShContent .= "done\n";

            // Renomeia os arquivos com a extensão especificada
            $batShContent .= "for f in \"$directoryDestiny/*.SOCIOCSV\"; do\n";
            $batShContent .= "    mv \"$f\" \"\${f%.SOCIOCSV}.CSV\"\n";
            $batShContent .= "done\n";
        }

        $file = "$ditPath/scripts/unzip_$name.$ext";
        file_put_contents($file, $batShContent);
        echo "Script $file gerado com sucesso!\n";
    }

    // Função principal
    private function init(): void
    {
        $mostRecentUrl = $this->getMostRecentURL();

        // Obtém os arquivos de sócios e estabelecimentos
        $sociosFiles = $this->getFilesFromDirectory($mostRecentUrl, 'Socio');
        $establishmentFiles = $this->getFilesFromDirectory($mostRecentUrl, 'Estabelecimento');
        $companiesFiles = $this->getFilesFromDirectory($mostRecentUrl, 'Empresa');

        // Gera os scripts de download e descompactação para sócios
        echo "Gerando scripts de download e descompactação para sócios...\n";
        $sociosName = 'socios';
        $this->generateDownloadScript($sociosFiles, $sociosName);
        $this->generateUnzipScript($sociosName, 'SOCIOCSV');

        // Gera os scripts de download e descompactação para estabelecimentos
        echo "Gerando scripts de download e descompactação para estabelecimentos...\n";
        $establishmentName = 'estabelecimentos';
        $this->generateDownloadScript($establishmentFiles, $establishmentName);
        $this->generateUnzipScript($establishmentName, 'ESTABELE');

        // Gera os scripts de download e descompactação para empresas
        echo "Gerando scripts de download e descompactação para empresas...\n";
        $companyName = 'empresas';
        $this->generateDownloadScript($companiesFiles, $companyName);
        $this->generateUnzipScript($companyName, 'EMPRECSV');
    }
}

