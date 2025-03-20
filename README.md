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
    git clone https://github.com/seuusuario/empresa-api.git
    cd empresa-api
    ```

2. Instale as dependências:
    ```sh
    composer install
    npm install
    ```

3. Configure o ambiente:
    ```sh
    cp .env.example .env
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

### Listar Empresas

- **URL:** `/api/empresas`
- **Método:** `GET`
- **Parâmetros de Consulta:**
  - `cnpj_basico` (opcional)
  - `razao_social` (opcional, suporta correspondências parciais)
- **Resposta:**
  ```json
  {
      "data": [
          {
              "cnpj": "12345678000195",
              "razao_social": "Nome da Empresa",
              "natureza_juridica": "Natureza",
              "qualificacao_responsavel": "Qualificação",
              "capital_social": "1000000",
              "porte_empresa": "Grande",
              "ente_federativo_responsavel": "Entidade",
              "estabelecimento": {
                  "nome_fantasia": "Nome Fantasia",
                  "logradouro": "Rua",
                  "numero": "123",
                  "bairro": "Bairro",
                  "municipio": "Cidade",
                  "uf": "Estado",
                  "cep": "12345678"
              },
              "qsa": [
                  {
                      "nome": "Nome do Sócio",
                      "qualificacao": "Qualificação"
                  }
              ],
              "atualizado_em": "01/01/2023 12:00:00"
          }
      ],
      "links": {
          "first": "URL",
          "last": "URL",
          "prev": "URL",
          "next": "URL"
      },
      "meta": {
          "current_page": 1,
          "from": 1,
          "last_page": 10,
          "path": "URL",
          "per_page": 15,
          "to": 15,
          "total": 150
      }
  }
