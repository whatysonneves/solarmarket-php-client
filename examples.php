<?php

require_once "SolarMarketAPI.class.php";

$solarMarketAPI = new SolarMarketAPI();

// Como usar a classe SolarMarketAPI para obter todos os produtos solares disponíveis no SolarMarket

// Pegando o Access Token para as consultas
echo "<h1>Pegando o Access Token para as consultas</h1>".PHP_EOL;
$getAccessToken = $solarMarketAPI->getAccessToken();
var_dump($getAccessToken);

// Listando todos os clientes
echo "<h1>Listando todos os clientes</h1>".PHP_EOL;
$listarClientes = $solarMarketAPI->listarClientes();
var_dump($listarClientes);

// Procurando clientes por nome
echo "<h1>Procurando clientes por nome</h1>".PHP_EOL;
$listarClientes = $solarMarketAPI->listarClientes("Anisia");
var_dump($listarClientes);

// Procurando cliente por ID
echo "<h1>Procurando cliente por ID</h1>".PHP_EOL;
$procurarCliente = $solarMarketAPI->procurarCliente(5);
var_dump($procurarCliente);

// Listando todos os projetos
echo "<h1>Listando todos os projetos</h1>".PHP_EOL;
$listarProjetos = $solarMarketAPI->listarProjetos();
var_dump($listarProjetos);
