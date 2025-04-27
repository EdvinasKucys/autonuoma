<?php

// sukuriame užklausų klasės objektą
$brandsObj = new brands();

if(!empty($id)) {
	// patikriname, ar šalinama markė nepriskirta modeliui
	$count = $brandsObj->getModelCountOfBrand($id);

	$removeErrorParameter = '';
	if($count == 0) {
		// šaliname markę
		$brandsObj->deleteBrand($id);
	} else {
		// nepašalinome, nes markė priskirta modeliui, rodome klaidos pranešimą
		$removeErrorParameter = '&remove_error=1';
	}

	// nukreipiame į markių puslapį
	common::redirect("index.php?module={$module}&action=list{$removeErrorParameter}");
	die();
}

?>