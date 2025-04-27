<?php
	
// sukuriame užklausų klasių objektus
$contractsObj = new contracts();
$servicesObj = new services();
$carsObj = new cars();
$employeesObj = new employees();
$customersObj = new customers();

$formErrors = null;
$data = array();


// gauname redaguojamos užsakytos paslaugos išrinkimo identifikatorius iš GET masyvo
$contractId = '';
if(isset($_GET['contractId'])) {
	$contractId = mysql::escapeFieldForSQL($_GET['contractId']);
}

// nustatome privalomus laukus
$required = array('kaina', 'kiekis');

// vartotojas paspaudė išsaugojimo mygtuką
if(!empty($_POST['submit'])) {
	// nustatome laukų validatorių tipus
	$validations = array (
		'kaina' => 'positivenumber',
		'kiekis' => 'positivenumber'
	);
	
	// sukuriame laukų validatoriaus objektą
	$validator = new validator($validations, $required);

	// laukai įvesti be klaidų
	if($validator->validate($_POST)) {
		// suformuojame laukų reikšmių masyvą SQL užklausai
		$data = $_POST;

		// gauname paslaugos id, galioja nuo ir kaina reikšmes {$price['fk_paslauga']}:{$price['galioja_nuo']}:{$price['kaina']}
		$tmp = explode("#", $data['paslauga']);
				
		$data['fk_paslauga'] = $tmp[0];
		$data['fk_kaina_galioja_nuo'] = $tmp[1];	


		// patikriname, ar nėra užsakyotos tokios pačios užsakytos paslaugos su tokia pačia kaina
		$kiekis = $contractsObj->checkIfOrderedServiceExists($contractId, $data['fk_paslauga'], $data['fk_kaina_galioja_nuo']);
		echo $kiekis;
		if($kiekis > 0) {
			// sudarome klaidų pranešimą
			$formErrors = "Paslauga su su pasirinkta kaina jau egzistuoja.";
			
			// laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
			$data = $_POST;
		} else {
			// įrašome naują užsakytą paslauga
			$contractsObj->insertOrderedService($contractId, $data['fk_paslauga'], $data['fk_kaina_galioja_nuo'], $data['kaina'], $data['kiekis']);

			// nukreipiame vartotoją į sutarties puslapį
			common::redirect("index.php?module={$module}&action=edit&id={$contractId}");
			die();
		}

		// nukreipiame vartotoją į sutarčių puslapį
		if($formErrors == null) {
			common::redirect("index.php?module={$module}&action=list");
			die();
		}
	} else {
		// gauname klaidų pranešimą
		$formErrors = $validator->getErrorHTML();
		// laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
		$data = $_POST;
	}
}

// įtraukiame šabloną
include "templates/{$module}/{$module}_service_form.tpl.php";

?>