<?php

// sukuriame užklausų klasės objektą
$contractsObj = new contracts();

// gauname redaguojamos užsakytos paslaugos išrinkimo identifikatorius iš GET masyvo
$contractId = '';
if(isset($_GET['contractId'])) {
	$contractId = mysql::escapeFieldForSQL($_GET['contractId']);
}

$serviceId = '';
if(isset($_GET['serviceId'])) {
	$serviceId = mysql::escapeFieldForSQL($_GET['serviceId']);
}

$dateFrom = '';
if(isset($_GET['dateFrom'])) {
	$dateFrom = mysql::escapeFieldForSQL($_GET['dateFrom']);
}

if(!empty($contractId) && !empty($serviceId) && !empty($dateFrom)) {
	// pašaliname užsakytą paslaugą
	$contractsObj->deleteOrderedService($contractId, $serviceId, $dateFrom);

	// nukreipiame į sutarčių puslapį
	common::redirect("index.php?module={$module}&action=edit&id={$contractId}");
	die();
}

?>