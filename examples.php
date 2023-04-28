<?php

require_once "SolarMarketAPI.class.php";

$solarMarketAPI = new SolarMarketAPI();

// Como usar a classe SolarMarketAPI para obter todos os produtos solares disponíveis no SolarMarket

// Pegando o Access Token para as consultas
$access_token = $solarMarketAPI->getAccessToken();

var_dump($access_token);
