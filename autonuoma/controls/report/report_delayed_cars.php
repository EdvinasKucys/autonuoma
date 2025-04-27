<?php

// sukuriame užklausų klasės objektą
$contractsObj = new contracts();

$formErrors = null;
$fields = array();
$formSubmitted = false;

$data = array();
if(empty($_POST['submit'])) {
	// rodome ataskaitos parametrų įvedimo formą
	include "templates/{$module}/{$module}_delayed_cars_form.tpl.php";
} else {
	$formSubmitted = true;

	// nustatome laukų validatorių tipus
	$validations = array (
			'dataNuo' => 'date',
			'dataIki' => 'date');

	// sukuriame validatoriaus objektą
	$validator = new validator($validations);

	if($validator->validate($_POST)) {
		// išrenkame ataskaitos duomenis
		$delayedCarsData = $contractsObj->getDelayedCars($_POST['dataNuo'], $_POST['dataIki']);
		
		// rodome ataskaitą
		include "templates/{$module}/{$module}_delayed_cars_show.tpl.php";
	} else {
		// gauname klaidų pranešimą
		$formErrors = $validator->getErrorHTML();
		// gauname įvestus laukus
		$fields = $_POST;

		// rodome ataskaitos parametrų įvedimo formą su klaidomis ir sustabdome scenarijaus vykdym1
		include "templates/{$module}/{$module}_delayed_cars_form.tpl.php";
	}

}