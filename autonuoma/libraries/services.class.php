<?php
/**
 * Papildomų paslaugų redagavimo klasė
 *
 * @author ISK
 */

class services {
	
	
	private $paslaugos_lentele = '';
	private $sutartys_lentele = '';
	private $paslaugu_kainos_lentele = '';
	private $uzsakytos_paslaugos_lentele = '';
	
	public function __construct() {
		$this->paslaugos_lentele = config::DB_PREFIX . 'paslaugos';
		$this->sutartys_lentele = config::DB_PREFIX . 'sutartys';
		$this->paslaugu_kainos_lentele = config::DB_PREFIX . 'paslaugu_kainos';
		$this->uzsakytos_paslaugos_lentele = config::DB_PREFIX . 'uzsakytos_paslaugos';
	}
	
	/**
	 * Paslaugų sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return paslaugų sąrašas pagal nurodytus rėžius
	 */
	public function getServicesList($limit = null, $offset = null) {
		if($limit) {
			$limit = mysql::escapeFieldForSQL($limit);
		}
		if($offset) {
			$offset = mysql::escapeFieldForSQL($offset);
		}
		
		$limitOffsetString = "";
		if(isset($limit)) {
			$limitOffsetString .= " LIMIT {$limit}";
		}
		if(isset($offset)) {
			$limitOffsetString .= " OFFSET {$offset}";
		}
		
		$query = "SELECT *
				FROM `{$this->paslaugos_lentele}`
				{$limitOffsetString}";
		$data = mysql::select($query);

		//
		return $data;
	}
	
	/**
	 * Paslaugų kiekio radimas
	 * @return paslaugų kiekis
	 */
	public function getServicesListCount() {
		$query = "SELECT COUNT(`{$this->paslaugos_lentele}`.`id`) as `kiekis`
				FROM `{$this->paslaugos_lentele}`";
		$data = mysql::select($query);
		
		//
		return $data[0]['kiekis'];
	}
	
	/**
	 * Paslaugos kainų sąrašo radimas
	 * @param type $serviceId
	 * @return paslaugos kainų sąrašas
	 */
	public function getServicePrices($serviceId) {
		$serviceId = mysql::escapeFieldForSQL($serviceId);
		
		$query = "SELECT *
				FROM `{$this->paslaugu_kainos_lentele}`
				WHERE `fk_paslauga`='{$serviceId}'";
		$data = mysql::select($query);
		
		//
		return $data;
	}
	
	/**
	 * Sutarčių, į kurias įtraukta paslauga, kiekio radimas
	 * @param type $serviceId
	 * @return sutarčių kiekis
	 */
	public function getContractCountOfService($serviceId) {
		$serviceId = mysql::escapeFieldForSQL($serviceId);
		
		$query = "SELECT COUNT(`{$this->sutartys_lentele}`.`nr`) AS `kiekis`
					FROM `{$this->paslaugos_lentele}`
						INNER JOIN `{$this->paslaugu_kainos_lentele}`
							ON `{$this->paslaugos_lentele}`.`id`=`{$this->paslaugu_kainos_lentele}`.`fk_paslauga`
						INNER JOIN `{$this->uzsakytos_paslaugos_lentele}`
							ON `{$this->paslaugu_kainos_lentele}`.`fk_paslauga`=`{$this->uzsakytos_paslaugos_lentele}`.`fk_paslauga`
						INNER JOIN `{$this->sutartys_lentele}`
							ON `{$this->uzsakytos_paslaugos_lentele}`.`fk_sutartis`=`{$this->sutartys_lentele}`.`nr`
				WHERE `{$this->paslaugos_lentele}`.`id`='{$serviceId}'";
		$data = mysql::select($query);
		
		//
		return $data[0]['kiekis'];
	}
	
	/**
	 * Paslaugos išrinkimas
	 * @param type $id
	 * @return paslaugos duomenų masyvas
	 */
	public function getService($id) {
		$id = mysql::escapeFieldForSQL($id);
		
		$query = "SELECT *
				FROM `{$this->paslaugos_lentele}`
				WHERE `id`='{$id}'";
		$data = mysql::select($query);

		//
		return $data[0];
	}
	
	/**
	 * Paslaugos įrašymas
	 * @param type $data
	 * @return įrašytos paslaugos ID
	 */
	public function insertService($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);
		
		$query = "INSERT INTO `{$this->paslaugos_lentele}`
						  (`pavadinimas`,
						   `aprasymas`)
				VALUES      ('{$data['pavadinimas']}',
						   '{$data['aprasymas']}')";
		mysql::query($query);
		
		//
		return mysql::getLastInsertedId();
	}
	
	/**
	 * Paslaugos atnaujinimas
	 * @param type $data
	 */
	public function updateService($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);
		
		$query = "UPDATE `{$this->paslaugos_lentele}`
				SET `pavadinimas`='{$data['pavadinimas']}',
				    `aprasymas`='{$data['aprasymas']}'
				WHERE `id`='{$data['id']}'";
		mysql::query($query);
	}
	
	/**
	 * Paslaugos šalinimas
	 * @param type $id
	 */
	public function deleteService($id) {
		$id = mysql::escapeFieldForSQL($id);
		
		$query = "DELETE FROM `{$this->paslaugos_lentele}`
				WHERE `id`='{$id}'";
		mysql::query($query);
	}
	
	/**
	 * Paslaugos kainų įrašymas
	 * @param type $serviceId
	 * @param type $galiojaNuo
	 * @param type $kaina
	 */
	public function insertServicePrices($serviceId, $galiojaNuo, $kaina) {
		$serviceId = mysql::escapeFieldForSQL($serviceId);
		$galiojaNuo = mysql::escapeFieldForSQL($galiojaNuo);
		$kaina = mysql::escapeFieldForSQL($kaina);
		
		$query = "INSERT INTO `{$this->paslaugu_kainos_lentele}`
						  (`fk_paslauga`,
						   `galioja_nuo`,
						   `kaina`)
				VALUES      ('{$serviceId}',
						   '{$galiojaNuo}',
						   '{$kaina}')";
		mysql::query($query);
	}
	
	/**
	 * Paslaugos kainos šalinimas
	 * @param type $serviceId
	 * @param type $galiojaNuo
	 * @param type $kaina
	 */
	public function deleteServicePrice($serviceId, $galiojaNuo, $kaina) {
		$serviceId = mysql::escapeFieldForSQL($serviceId);
		$galiojaNuo = mysql::escapeFieldForSQL($galiojaNuo);
		$kaina = mysql::escapeFieldForSQL($kaina);
		
		$query = "DELETE FROM `{$this->paslaugu_kainos_lentele}`
				WHERE `fk_paslauga`='{$serviceId}' AND `galioja_nuo`='{$galiojaNuo}' AND `kaina`='{$kaina}'";
		mysql::query($query);
	}

	/**
	 * Visų paslaugos kainų šalinimas
	 * @param type $serviceId
	 * @param type $clause
	 */
	public function deleteAllServicePrices($serviceId) {
		$serviceId = mysql::escapeFieldForSQL($serviceId);

		$query = "DELETE FROM `{$this->paslaugu_kainos_lentele}`
				WHERE `fk_paslauga`='{$serviceId}'";
		mysql::query($query);
	}
	
	/**
	 * Užsakytų paslaugų išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return paslaugų sąrašas pagal nurodytus datos rėžius
	 */
	public function getOrderedServices($dateFrom, $dateTo) {
		$dateFrom = mysql::escapeFieldForSQL($dateFrom);
		$dateTo = mysql::escapeFieldForSQL($dateTo);
		
		$whereClauseString = "";
		if(!empty($dateFrom)) {
			$whereClauseString .= " WHERE `{$this->sutartys_lentele}`.`sutarties_data`>='{$dateFrom}'";
			if(!empty($dateTo)) {
				$whereClauseString .= " AND `{$this->sutartys_lentele}`.`sutarties_data`<='{$dateTo}'";
			}
		} else {
			if(!empty($dateTo)) {
				$whereClauseString .= " WHERE `{$this->sutartys_lentele}`.`sutarties_data`<='{$dateTo}'";
			}
		}
		
		$query = "SELECT `id`,
					  `pavadinimas`,
					  SUM(`kiekis`) AS `uzsakyta`,
					  SUM(`kiekis`*`{$this->uzsakytos_paslaugos_lentele}`.`kaina`) AS `bendra_suma`
					FROM `{$this->paslaugos_lentele}`
						INNER JOIN `{$this->uzsakytos_paslaugos_lentele}`
							ON `{$this->paslaugos_lentele}`.`id`=`{$this->uzsakytos_paslaugos_lentele}`.`fk_paslauga`
						INNER JOIN `{$this->sutartys_lentele}`
							ON `{$this->uzsakytos_paslaugos_lentele}`.`fk_sutartis`=`{$this->sutartys_lentele}`.`nr`
					{$whereClauseString}
					GROUP BY `{$this->paslaugos_lentele}`.`id`
					ORDER BY `bendra_suma` DESC";
		$data = mysql::select($query);

		//
		return $data;
	}

	/**
	 * Užsakytos paslaugos išrinkimas sutartyje
	 * @param type $contractId sutarties id
	 * @param type $dateFrom užsakytos paslaugos kainos galiojimo pradžios data
	 * @param type $serviceId užsakytos paslaugos id
	 * @return užsakytos paslaugos duomenų masyvas
	 */
	public function getOrderedService($contractId, $dateFrom, $serviceId) {
		$contractId = mysql::escapeFieldForSQL($contractId);
		$dateFrom = mysql::escapeFieldForSQL($dateFrom);
		$serviceId = mysql::escapeFieldForSQL($serviceId);
		
		$query = "SELECT `fk_sutartis`,
					  `fk_kaina_galioja_nuo`,
					  `fk_paslauga`,
					  `kiekis`,
					  `kaina`
				FROM `{$this->uzsakytos_paslaugos_lentele}`
				WHERE `fk_sutartis`='{$contractId}' AND `fk_kaina_galioja_nuo`='{$dateFrom}' AND `fk_paslauga`='{$serviceId}'";
		$data = mysql::select($query);

		//
		return $data[0];
	}

	/**
	 * Užsakytų paslaugų ataskaitos duomenų išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return užsakytų paslaugų kiekis ir suma
	 */
	public function getStatsOfOrderedServices($dateFrom, $dateTo) {
		$dateFrom = mysql::escapeFieldForSQL($dateFrom);
		$dateTo = mysql::escapeFieldForSQL($dateTo);
		
		$whereClauseString = "";
		if(!empty($dateFrom)) {
			$whereClauseString .= " WHERE `{$this->sutartys_lentele}`.`sutarties_data`>='{$dateFrom}'";
			if(!empty($dateTo)) {
				$whereClauseString .= " AND `{$this->sutartys_lentele}`.`sutarties_data`<='{$dateTo}'";
			}
		} else {
			if(!empty($dateTo)) {
				$whereClauseString .= " WHERE `{$this->sutartys_lentele}`.`sutarties_data`<='{$dateTo}'";
			}
		}
		
		$query = "SELECT SUM(`kiekis`) AS `uzsakyta`,
					  SUM(`kiekis`*`{$this->uzsakytos_paslaugos_lentele}`.`kaina`) AS `bendra_suma`
				FROM `{$this->paslaugos_lentele}`
					INNER JOIN `{$this->uzsakytos_paslaugos_lentele}`
						ON `{$this->paslaugos_lentele}`.`id`=`{$this->uzsakytos_paslaugos_lentele}`.`fk_paslauga`
					INNER JOIN `{$this->sutartys_lentele}`
						ON `{$this->uzsakytos_paslaugos_lentele}`.`fk_sutartis`=`{$this->sutartys_lentele}`.`nr`
				{$whereClauseString}";
		$data = mysql::select($query);

		//
		return $data;
	}
}