<?php

// sukuriame užklausų klasės objektą
$carsObj = new cars();

// suskaičiuojame bendrą įrašų kiekį
$elementCount = $carsObj->getCarListCount();

// sukuriame puslapiavimo klasės objektą
$paging = new paging(config::NUMBER_OF_ROWS_IN_PAGE);

// suformuojame sąrašo puslapius
$paging->process($elementCount, $pageId);

// išrenkame nurodyto puslapio automobilius
$data = $carsObj->getCarList($paging->size, $paging->first);	

// įtraukiame šabloną
include "templates/{$module}/{$module}_list.tpl.php";

?>