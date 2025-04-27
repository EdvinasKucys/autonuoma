<?php

// sukuriame užklausų klasės objektą
$brandsObj = new brands();

$formErrors = null;
$data = array();

// nustatome privalomus laukus
$required = array('pavadinimas');

// maksimalūs leidžiami laukų ilgiai
$maxLengths = array (
	'pavadinimas' => 20
);

// paspaustas išsaugojimo mygtukas
if(!empty($_POST['submit'])) {
	// nustatome laukų validatorių tipus
	$validations = array (
		'pavadinimas' => 'anything');

	// sukuriame validatoriaus objektą
	$validator = new validator($validations, $required, $maxLengths);

	if($validator->validate($_POST)) {
		// įrašome naują įrašą
		$brandsObj->insertBrand($_POST);

		// nukreipiame į markių puslapį
		common::redirect("index.php?module={$module}&action=list");
		die();
	} else {
		// gauname klaidų pranešimą
		$formErrors = $validator->getErrorHTML();
		// gauname įvestus laukus
		$data = $_POST;
	}
}

// įtraukiame šabloną
include "templates/{$module}/{$module}_form.tpl.php";

?>