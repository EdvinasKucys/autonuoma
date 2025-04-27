<?php

// sukuriame užklausų klasės objektą
$brandsObj = new brands();

// suskaičiuojame bendrą įrašų kiekį
$elementCount = $brandsObj->getBrandListCount();

// sukuriame puslapiavimo klasės objektą
$paging = new paging(config::NUMBER_OF_ROWS_IN_PAGE);

// suformuojame sąrašo puslapius
$paging->process($elementCount, $pageId);

// išrenkame nurodyto puslapio markes
$data = $brandsObj->getBrandList($paging->size, $paging->first);

// įtraukiame šabloną
include "templates/{$module}/{$module}_list.tpl.php";

?>