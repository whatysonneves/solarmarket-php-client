SolarMarketAPI PHP Client
=========================

Este é um exemplo de cliente PHP para consumir a API do SolarMarket.

Instalação
----------

Para instalar este cliente PHP, siga os seguintes passos:

1.  Baixe o [Composer](https://getcomposer.org/) caso não tenha.
2.  Clone este repositório em sua máquina:

```bash
git clone https://github.com/whatysonneves/solarmarket-php-client.git
```

3.  Navegue até o diretório do projeto:

```bash
cd solarmarket-php-client
```

4.  Instale os pacotes do Composer:

```bash
composer install
```

5.  Renomeie o arquivo `.env.example` para `.env`;
6.  Substitua `api-key-aqui` pela chave de API fornecida pelo SolarMarket dentro do arquivo `.env`.

Como usar
---------

Após a instalação, você pode usar a classe `SolarMarketAPI` para consumir a API do SolarMarket. Veja o arquivo `examples.php`.
Lembre-se de ter as variáveis de ambiente definidas corretamente no arquivo `.env` antes de usar a classe `SolarMarketAPI`.

Documentação
------------

A documentação completa da API do SolarMarket pode ser encontrada em: [https://solarmarket.docs.apiary.io/](https://solarmarket.docs.apiary.io/)
