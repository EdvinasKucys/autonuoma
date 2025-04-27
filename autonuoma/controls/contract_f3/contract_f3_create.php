<?php
	
// sukuriame užklausų klasių objektus
$contractsObj = new contracts();
$servicesObj = new services();
$carsObj = new cars();
$employeesObj = new employees();
$customersObj = new customers();

$formErrors = null;
$data = array();
$data['uzsakytos_paslaugos'] = array();

// nustatome privalomus laukus
$required = array('nr', 'sutarties_data', 'nuomos_data_laikas', 'planuojama_grazinimo_data_laikas', 'pradine_rida', 'kaina', 'degalu_kiekis_paimant', 'busena', 'fk_klientas', 'fk_darbuotojas', 'fk_automobilis', 'fk_grazinimo_vieta', 'fk_paemimo_vieta', 'kiekiai');

// vartotojas paspaudė išsaugojimo mygtuką
if(!empty($_POST['submit'])) {
	// nustatome laukų validatorių tipus
	$validations = array (
		'nr' => 'positivenumber',
		'sutarties_data' => 'date',
		'nuomos_data_laikas' => 'datetime',
		'planuojama_grazinimo_data_laikas' => 'datetime',
		'faktine_grazinimo_data_laikas' => 'datetime',
		'pradine_rida' => 'int',
		'galine_rida' => 'int',
		'kaina' => 'price',
		'degalu_kiekis_paimant' => 'int',
		'dagalu_kiekis_grazinus' => 'int',
		'busena' => 'positivenumber',
		'fk_klientas' => 'alfanum',
		'fk_darbuotojas' => 'alfanum',
		'fk_automobilis' => 'positivenumber',
		'fk_grazinimo_vieta' => 'positivenumber',
		'fk_paemimo_vieta' => 'positivenumber',
		'kiekis' => 'int');
	
	// sukuriame laukų validatoriaus objektą
	$validator = new validator($validations, $required);

	// laukai įvesti be klaidų
	if($validator->validate($_POST)) {
		// patikriname, ar nėra sutarčių su tokiu pačiu numeriu
		$kiekis = $contractsObj->checkIfContractNrExists($_POST['nr']);

		if($kiekis > 0) {
			// sudarome klaidų pranešimą
			$formErrors = "Sutartis su įvestu numeriu jau egzistuoja.";
			// laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
			$data = $_POST;
		} else {
			// įrašome naują sutartį
			$contractsObj->insertContract($_POST);

			// nukreipiame vartotoją į sutarčių puslapį
			common::redirect("index.php?module={$module}&action=edit&id={$_POST['nr']}");
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
include "templates/{$module}/{$module}_form.tpl.php";

?>