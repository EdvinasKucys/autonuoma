<?php

// sukuriame užklausų klasių objektus
$productObj = new product();
$manufacturerObj = new manufacturer();
$categoryObj = new category();
$warehouseObj = new Warehouse();

$formErrors = null;
$data = array();
$data['warehousedProducts'] = array();

// nustatome privalomus laukus
$required = array('id', 'pavadinimas', 'kaina', 'svoris', 'fk_GAMINTOJASgamintojo_id', 'fk_KATEGORIJAid_KATEGORIJA');
// vartotojas paspaudė išsaugojimo mygtuką
if(!empty($_POST['submit'])) {
    // Define field validation types
	$maxLengths = array (
		'pavadinimas' => 200,
		'aprasymas'=> 255,
		'id'=> 64,
		'medziaga'=> 100,
		'aprasymas'=> 255,
		'fk_GAMINTOJASgamintojo_ids'=> 64,
		'fk_KATEGOTIJAid_KATEGORIJA'=> 11,
	);

    $validations = array (
        'id' => 'alfanum',
        'pavadinimas' => 'anything',
        'aprasymas' => 'anything',
        'kaina' => 'price',
        'svoris' => 'float',
        'medziaga' => 'anything',
        'fk_GAMINTOJASgamintojo_id' => 'alfanum',
        'fk_KATEGORIJAid_KATEGORIJA' => 'alfanum',
		'sandelis' => 'anything',
		'kiekis' => 'int'
    );
	
	// sukuriame laukų validatoriaus objektą
	$validator = new validator($validations, $required);

	// laukai įvesti be klaidų
	if($validator->validate($_POST)) {
		// patikriname, ar nėra sutarčių su tokiu pačiu numeriu
		$kiekis = $productObj->checkIfProductidExists($_POST['id']);
		
		$productid = $productObj->insertProduct($_POST);


		if($kiekis > 0) {
			// sudarome klaidų pranešimą
			$formErrors = "Sutartis su įvestu numeriu jau egzistuoja.";
			// laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
			$data = $_POST;
		} else {
			// įrašome naują sutartį
			$productid = $productObj->insertProduct($_POST);

			// įrašome užsakytas paslaugas
			foreach($_POST['sandelis'] as $keyForm => $warehouseForm) {

				// gauname paslaugos id, galioja nuo ir kaina reikšmes {$price['fk_paslauga']}#{$price['galioja_nuo']}
				$warehouseId = $warehouseForm;

				$inventorytObj->insertInventoryItem($productId, $warehouseId, $_POST['kiekis'][$keyForm]);
			}
			
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

		$data['warehousedProducts'] = array();
		if(isset($_POST['sandelis'])) {
			$i = 0;
			foreach($_POST['sandelis'] as $key => $val) {
				// gauname paslaugos id, galioja nuo ir kaina reikšmes {$price['fk_paslauga']}#{$price['galioja_nuo']}
				
				$warehouseId = $val;
				
				$data['warehousedProducts'][$i]['fk_Prekeid'] = $warehouseid;
				$data['warehousedProducts'][$i]['kiekis'] =  $_POST['kiekis'][$key];

				$i++;
			}
		}
	}
}

// į užsakytų paslaugų masyvo pradžią įtraukiame tuščią reikšmę, kad užsakytų paslaugų formoje
// būtų visada išvedami paslėpti formos laukai, kuriuos galėtume kopijuoti ir pridėti norimą
// kiekį paslaugų
array_unshift($data['warehousedProducts'], array());

// įtraukiame šabloną
include "templates/{$module}/{$module}_form.tpl.php";

?>