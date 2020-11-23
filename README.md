# Teste Codificar

Resolução do teste do processo seletivo 11/20.

## Requisitos
Ter uma base de dados MySQL criada com o nome 'api' ou criá-la pelos arquivos 'structure.sql' e 'data.sql'. Se preferir, pode-se usar também os comandos do migrate.

## Instalação

```bash
$ composer install
$ php artisan serve
$ php artisan migrate
```

## Buscando os dados

```python
# preenche a tabela de deputados:
$ php artisan populate-deputados get-currents

#  preenche a tabela de lista_telefonica:
$ php artisan populate-deputados get-data

#  preenche a tabela de refund_dates:
$ php artisan init-verbas get-dates

#  preenche a tabela de lista_telefonica:
$ php artisan init-verbas get-refunds
```

## Endpoints
Deputados com mais verbas:  [http://localhost:8000/api/refunds/2019](http://localhost:8000/api/refunds/2019)

Redes sociais mais utilizadas pelos deputados: [http://localhost:8000/api/social-ranking/](http://localhost:8000/api/social-ranking/)

### Observações:
Não foram encontradas dados sobre redes sociais dos deputados no seguinte endpoint da ALMG:
[http://dadosabertos.almg.gov.br/ws/deputados/lista_telefonica](http://dadosabertos.almg.gov.br/ws/deputados/lista_telefonica)
