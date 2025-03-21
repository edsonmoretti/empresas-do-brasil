# API de Empresas

## Visão Geral

A API de Empresas fornece endpoints para gerenciar empresas e seus estabelecimentos. Esta API é construída com Laravel e oferece funcionalidades para filtrar, paginar e recuperar informações detalhadas sobre empresas e seus estabelecimentos relacionados.

## Requisitos

- PHP 8.2 ou superior
- Composer
- Laravel 12.0 ou superior

## Instalação

1. Clone o repositório:
    ```sh
    git clone https://github.com/edsonmoretti/empresas-do-brasil
    cd empresas-do-brasil
    ```

2. Instale as dependências:
    ```sh
    composer install
    ```

3. Configure o ambiente:
    ```sh
    cp .env.example .env # (configure o arquivo .env)
    php artisan key:generate
    ```

4. Execute as migrações:
    ```sh
    php artisan migrate
    ```

5. Inicie o servidor de desenvolvimento:
    ```sh
    php artisan serve
    ```

## Endpoints
TODO: Documentar os endpoints da API
