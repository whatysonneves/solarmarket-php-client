<?php

require_once "./bootstrap.php";

use GuzzleHttp\Client;

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
			"json" => [
				"query" => "mutation (\$APIToken: String!) { generateAPIAccessToken(userAPIToken: \$APIToken) }",
				"variables" => [
					"APIToken" => $this->apiKey,
				]
			],
		]);

		$data = json_decode($response->getBody(), true);

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

	/**
	 * Retorna uma lista de clientes da SolarMarket.
	 *
	 * @param string $name|null
	 * @param int    $quantidade
	 * @param int    $offset
	 *
	 * @return array
	 *
	 * @throws RequestException
	 */
	public function listarClientes($nome = null, $quantidade = 10, $offset = 0)
	{
		$response = $this->client->post("/graphql", [
			"headers" => [
				"Authorization" => "Bearer {$this->getAccessToken()}",
			],
			"json" => [
				"query" => '
					query ListClients($first: Int, $offset: Int, $name: String) {
						listClients(first: $first, offset: $offset, name: $name) {
							id
							user {
								id
								name
								email
								phone
								createdAt
							}
							representative {
								id
								name
								email
								phone
								createdAt
							}
							name
							company
							cnpjCpf
							email
							phone
							secondaryPhone
							adress
							number
							complement
							neighborhood
							city
							state
							createdAt
							deletedAt
						}
					}
				',
				"variables" => [
					"first" => $quantidade,
					"offset" => $offset,
					"name" => $nome,
				],
			],
		]);

		return $this->responseBody($response, "listClients");
	}

	/**
	 * Procura um cliente pelo ID.
	 *
	 * @param string $id
	 * @return array
	 * @throws GuzzleException
	 */
	public function procurarCliente($id)
	{
		$response = $this->client->post("/graphql", [
			"headers" => [
				"Authorization" => "Bearer {$this->getAccessToken()}",
			],
			"json" => [
				"query" => '
					query FindClient($id: ID!) {
						findClient(id: $id) {
							id
							user {
								id
								name
								email
								phone
								createdAt
							}
							representative {
								id
								name
								email
								phone
								createdAt
							}
							name
							company
							cnpjCpf
							email
							phone
							secondaryPhone
							adress
							number
							complement
							neighborhood
							city
							state
							createdAt
							deletedAt
						}
					}
				',
				"variables" => [
					"id" => $id,
				],
			],
		]);

		return $this->responseBody($response, "findClient");
	}

	/**
	 * Retorna uma lista de projetos de acordo com os parâmetros informados.
	 *
	 * @param string|null $nome
	 * @param int|null    $clientId
	 * @param bool|null   $crm
	 * @param string|null $statusProject
	 * @param string|null $createdAtStart
	 * @param string|null $createdAtFinish
	 * @param array|null  $responsibleId
	 * @param array|null  $representativeId
	 * @param bool|null   $deleted
	 * @param int         $quantidade
	 * @param int         $offset
	 * @return array      Array de objetos Projeto com os dados dos projetos encontrados.
	 */
	public function listarProjetos(
		?string $nome = null,
		?int $clientId = null,
		?bool $crm = null,
		?string $statusProject = null,
		?string $createdAtStart = null,
		?string $createdAtFinish = null,
		?array $responsibleId = null,
		?array $representativeId = null,
		?bool $deleted = null,
		int $quantidade = 10,
		int $offset = 0
	) {
		$response = $this->client->post("/graphql", [
			"headers" => [
				"Authorization" => "Bearer {$this->getAccessToken()}",
			],
			"json" => [
				"query" => '
					query ListProjects(
					$first: Int, $offset: Int, $name: String, $clientId: Int,
					$crm: Boolean, $statusProject: StatusProject,
					$createdAtStart: Date, $createdAtFinish: Date, $responsibleId: [Int],
					$representativeId: [Int], $deleted: Boolean
					) {
						listProjects(
						first: $first, offset: $offset, name: $name, clientId: $clientId,
						crm: $crm, statusProject: $statusProject,
						createdAtStart: $createdAtStart, createdAtFinish: $createdAtFinish, responsibleId: $responsibleId,
						representativeId: $representativeId, deleted: $deleted
						) {
							id
							name
							client {
								id
							}
							responsible {
								id
								name
							}
							qntProposals
						}
					}
				',
				"variables" => [
					"first" => $quantidade,
					"offset" => $offset,
					"name" => $nome,
					"clientId" => $clientId,
					"crm" => $crm,
					"statusProject" => $statusProject,
					"createdAtStart" => $createdAtStart,
					"createdAtFinish" => $createdAtFinish,
					"responsibleId" => $responsibleId,
					"representativeId" => $representativeId,
					"deleted" => $deleted,
				],
			],
		]);

		return $this->responseBody($response, "listProjects");
	}

	/**
	 * Retorna o valor da chave especificada do corpo da resposta da requisição em formato JSON.
	 *
	 * @param $response
	 * @param $key
	 * @return mixed
	 */
	public function responseBody($response, $key)
	{
		$responseBody = json_decode($response->getBody()->getContents(), true);
		$this->checkReturn($responseBody);

		if(!is_null($responseBody) && !is_null($responseBody["data"])) {
			return $responseBody["data"][$key];
		}

		return $responseBody;
	}

	/**
	 * Verifica o retorno da API e apaga o arquivo de token de acesso caso o token tenha expirado.
	 *
	 * @param array $response
	 * @return void
	 */
	public function checkReturn($responseBody)
	{
		if(
			is_array($responseBody) &&
			array_key_exists("success", $responseBody) &&
			!$responseBody["success"] &&
			$responseBody["errors"][0]["code"] == "JWT-EXPIRED"
		) {
			if(!file_exists($this->accessTokenFile)) {
			}
			unlink($this->accessTokenFile);
		}
	}
}
