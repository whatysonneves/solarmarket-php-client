<?php

require_once "./bootstrap.php";

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class SolarMarketAPI {

	private $client;
	private $apiKey;
	private $apiUrl;

	/**
	 * Inicializa a instância da classe, carregando a chave e a URL da API a partir do arquivo .env.
	 */
	public function __construct() {
		$this->apiKey = $_ENV["SOLARMARKET_API_KEY"];
		$this->apiUrl = $_ENV["SOLARMARKET_API_URL"];

		$this->client = new Client([
			"base_uri" => $this->apiUrl,
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
	public function gerarAPIAccessToken(): ?string
	{
		$response = $this->client->post("/api/v1", [
			RequestOptions::JSON => [ "query" => "mutation { generateAPIAccessToken(userAPIToken: {$this->apiKey}) }" ],
		]);

		if($response->getStatusCode() !== 200) {
			throw new Exception("Erro ao obter o API Access Token +https://solarmarket.docs.apiary.io/#reference/0/authentication/authenticate");
		}

		$body = $response->getBody();
		$data = json_decode($body, true);

		if(isset($data["data"]["generateAPIAccessToken"])) {
			return $data["data"]["generateAPIAccessToken"];
		}

		return null;
	}

}
