<?php

// sukuriame užklausų klasių objektus
$productObj = new product();
$manufacturerObj = new manufacturer();
$categoryObj = new category();
$inventoryObj = new inventory();

$formErrors = null;
$data = array();
$data['prekes'] = array();

// nustatome privalomus laukus
$required = array('id', 'pavadinimas', 'kaina', 'svoris', 'fk_GAMINTOJASgamintojo_id', 'fk_KATEGORIJAid_KATEGORIJA', 'kiekiai');
// vartotojas paspaudė išsaugojimo mygtuką
if(!empty($_POST['submit'])) {
    // Define field validation types
    $validations = array (
        'id' => 'alfanum',
        'pavadinimas' => 'anything',
        'aprasymas' => 'anything',
        'kaina' => 'price',
        'svoris' => 'float',
        'medziaga' => 'anything',
        'fk_GAMINTOJASgamintojo_id' => 'alfanum',
        'fk_KATEGORIJAid_KATEGORIJA' => 'positivenumber',
        'sandelio_id' => 'alfanum',
        'kiekis' => 'int'
    );
	
	// sukuriame laukų validatoriaus objektą
	$validator = new validator($validations, $required);

	// laukai įvesti be klaidų
	if($validator->validate($_POST)) {
		// patikriname, ar nėra sutarčių su tokiu pačiu numeriu
		$kiekis = $productObj->checkIfProductidExists($_POST['id']);

		if($kiekis > 0) {
			// sudarome klaidų pranešimą
			$formErrors = "Sutartis su įvestu numeriu jau egzistuoja.";
			// laukų reikšmių kintamajam priskiriame įvestų laukų reikšmes
			$data = $_POST;
		} else {
			// įrašome naują sutartį
			$productObj->insertProduct($_POST);

			// įrašome užsakytas paslaugas
			foreach($_POST['sandelis'] as $keyForm => $sandelisForm) {

				// gauname paslaugos id, galioja nuo ir kaina reikšmes {$price['fk_paslauga']}#{$price['galioja_nuo']}
				$sandelioId = $sandelisForm;
				$inventorytObj->insertInventoryItem($_POST['nr'], $serviceId, $priceFrom, $_POST['paslaugos_kaina'][$keyForm], $_POST['paslaugos_kiekis'][$keyForm]);
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

		$data['uzsakytos_paslaugos'] = array();
		if(isset($_POST['paslauga'])) {
			$i = 0;
			foreach($_POST['paslauga'] as $key => $val) {
				// gauname paslaugos id, galioja nuo ir kaina reikšmes {$price['fk_paslauga']}#{$price['galioja_nuo']}
				$tmp = explode("#", $val);
				
				$serviceId = $tmp[0];
				$priceFrom = $tmp[1];
				
				$data['uzsakytos_paslaugos'][$i]['fk_paslauga'] = $serviceId;
				$data['uzsakytos_paslaugos'][$i]['fk_kaina_galioja_nuo'] = $priceFrom;
				$data['uzsakytos_paslaugos'][$i]['kaina'] = $_POST['paslaugos_kaina'][$key];
				$data['uzsakytos_paslaugos'][$i]['kiekis'] = $_POST['paslaugos_kiekis'][$key];

				$i++;
			}
		}
	}
}

// į užsakytų paslaugų masyvo pradžią įtraukiame tuščią reikšmę, kad užsakytų paslaugų formoje
// būtų visada išvedami paslėpti formos laukai, kuriuos galėtume kopijuoti ir pridėti norimą
// kiekį paslaugų
array_unshift($data['uzsakytos_paslaugos'], array());

// įtraukiame šabloną
include "templates/{$module}/{$module}_form.tpl.php";

?>