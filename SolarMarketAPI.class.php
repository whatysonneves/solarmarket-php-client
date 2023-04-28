<?php

require_once "./bootstrap.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class SolarMarketAPI {

	private $client;
	private $apiKey;
	private $apiUrl;
	private $accessTokenFile;

	/**
	 * Inicializa a instância da classe, carregando a chave e a URL da API a partir do arquivo .env.
	 */
	public function __construct() {
		$this->apiKey = $_ENV["SOLARMARKET_API_KEY"];
		$this->apiUrl = $_ENV["SOLARMARKET_API_URL"];
		$this->accessTokenFile = $_ENV["SOLARMARKET_ACCESS_TOKEN_FILE"];

		$this->client = new Client([
			"base_uri" => $this->apiUrl,
			"verify" => false, // desativar esta linha ao subir para o servidor
			"headers" => [
				// "Authorization" => "Bearer {$this->apiKey}",
				"Content-Type" => "application/json",
				"User-Agent" => "Mozilla/5.0 (compatible; WhatysonNeves/1.0; +https://github.com/whatysonneves/solarmarket-php-client)",
			],
		]);
	}

	/**
	 * Gera um novo access token.
	 *
	 * @return string|null
	 * @throws GuzzleException
	 */
	protected function gerarAPIAccessToken()
	{
		$response = $this->client->post("/api/v1", [
			RequestOptions::JSON => [
				"query" => "mutation (\$APIToken: String!) { generateAPIAccessToken(userAPIToken: \$APIToken) }",
				"variables" => [
					"APIToken" => $this->apiKey,
				]
			],
		]);

		if($response->getStatusCode() !== 200) {
			throw new Exception("Erro ao obter o API Access Token +https://solarmarket.docs.apiary.io/#reference/0/authentication/authenticate");
		}

		$body = $response->getBody();
		$data = json_decode($body, true);

		if(isset($data["data"]["generateAPIAccessToken"])) {
			return $this->salvarAccessToken($data["data"]["generateAPIAccessToken"]);
		}

		return null;
	}

	/**
	 * Salva o access token em um arquivo JSON com informações de expiração.
	 * Este método pode ser atualizado para persistir em banco de dados.
	 *
	 * @param string $accessToken
	 * @return void
	 */
	protected function salvarAccessToken($accessToken)
	{
		$expires = time() + 6 * 3600; // Expira em 6 horas
		$data = json_encode([
			"access_token" => $accessToken,
			"expires" => $expires
		], JSON_PRETTY_PRINT);

		// Abre o arquivo em modo de escrita, zerando o conteúdo anterior
		$file = fopen($this->accessTokenFile, "w");
		fwrite($file, $data);
		fclose($file);

		return $accessToken;
	}

	/**
	 * Retorna o access token persistido em um arquivo JSON.
	 * Este método pode ser atualizado para recuperar de banco de dados.
	 *
	 * @return string|null
	 */
	public function getAccessToken()
	{
		if(!file_exists($this->accessTokenFile)) {
			return $this->gerarAPIAccessToken();
		}

		$accessTokenData = json_decode(file_get_contents($this->accessTokenFile), true);

		if($accessTokenData && $accessTokenData["access_token"] && $accessTokenData["expires"] > time()) {
			return $accessTokenData["access_token"];
		}

		return $this->gerarAPIAccessToken();
	}

}
