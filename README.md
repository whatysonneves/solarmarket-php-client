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
cd solarmarket-api-php-client
```

4.  Instale os pacotes do Composer:

```bash
composer install
```

5.  Crie um arquivo `.env` na raiz do projeto com as seguintes variáveis de ambiente:

```ini
SOLARMARKET_API_URL="https://api.solarmarket.com.br/"
SOLARMARKET_API_KEY="api-key-aqui"
```

Substitua `api-key-aqui` pela chave de API fornecida pelo SolarMarket.