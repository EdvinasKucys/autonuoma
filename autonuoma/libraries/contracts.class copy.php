<?php
/**
 * Sutarčių redagavimo klasė
 *
 * @author ISK
 */

class contracts {

	private $sutartys_lentele = '';
	private $darbuotojai_lentele = '';
	private $klientai_lentele = '';
	private $sutarties_busenos_lentele = '';
	private $uzsakytos_paslaugos_lentele = '';
	private $aiksteles_lentele = '';
	private $paslaugu_kainos_lentele = '';
	private $paslaugos_lentele = '';

	public function __construct() {
		$this->sutartys_lentele = config::DB_PREFIX . 'sutartys';
		$this->darbuotojai_lentele = config::DB_PREFIX . 'darbuotojai';
		$this->klientai_lentele = config::DB_PREFIX . 'klientai';
		$this->sutarties_busenos_lentele = config::DB_PREFIX . 'sutarties_busenos';
		$this->uzsakytos_paslaugos_lentele = config::DB_PREFIX . 'uzsakytos_paslaugos';
		$this->aiksteles_lentele = config::DB_PREFIX . 'aiksteles';
		$this->paslaugu_kainos_lentele = config::DB_PREFIX . 'paslaugu_kainos';
		$this->paslaugos_lentele = config::DB_PREFIX . 'paslaugos';
	}
	
	/**
	 * Sutarčių sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getContractList($limit, $offset) {
		$limit = mysql::escapeFieldForSQL($limit);
		$offset = mysql::escapeFieldForSQL($offset);

		$query = "SELECT `{$this->sutartys_lentele}`.`nr`,
					  `{$this->sutartys_lentele}`.`sutarties_data`,
					  `{$this->darbuotojai_lentele}`.`vardas` AS `darbuotojo_vardas`,
					  `{$this->darbuotojai_lentele}`.`pavarde` AS `darbuotojo_pavarde`,
					  `{$this->klientai_lentele}`.`vardas` AS `kliento_vardas`,
					  `{$this->klientai_lentele}`.`pavarde` AS `kliento_pavarde`,
					  `{$this->sutarties_busenos_lentele}`.`name` AS `busena`
				FROM `{$this->sutartys_lentele}`
					LEFT JOIN `{$this->darbuotojai_lentele}`
						ON `{$this->sutartys_lentele}`.`fk_darbuotojas`=`{$this->darbuotojai_lentele}`.`tabelio_nr`
					LEFT JOIN `{$this->klientai_lentele}`
						ON `{$this->sutartys_lentele}`.`fk_klientas`=`{$this->klientai_lentele}`.`asmens_kodas`
					LEFT JOIN `{$this->sutarties_busenos_lentele}`
						ON `{$this->sutartys_lentele}`.`busena`=`{$this->sutarties_busenos_lentele}`.`id`
				LIMIT {$limit} OFFSET {$offset}";
		$data = mysql::select($query);
		
		//
		return $data;
	}
	
	/**
	 * Sutarčių kiekio radimas
	 * @return type
	 */
	public function getContractListCount() {
		$query = "SELECT COUNT(`{$this->sutartys_lentele}`.`nr`) AS `kiekis`
					FROM `{$this->sutartys_lentele}`
						LEFT JOIN `{$this->darbuotojai_lentele}`
							ON `{$this->sutartys_lentele}`.`fk_darbuotojas`=`{$this->darbuotojai_lentele}`.`tabelio_nr`
						LEFT JOIN `{$this->klientai_lentele}`
							ON `{$this->sutartys_lentele}`.`fk_klientas`=`{$this->klientai_lentele}`.`asmens_kodas`
						LEFT JOIN `{$this->sutarties_busenos_lentele}`
							ON `{$this->sutartys_lentele}`.`busena`=`{$this->sutarties_busenos_lentele}`.`id`";
		$data = mysql::select($query);
		
		//
		return $data[0]['kiekis'];
	}
	
	/**
	 * Sutarties išrinkimas
	 * @param type $nr
	 * @return type
	 */
	public function getContract($nr) {
		$nr = mysql::escapeFieldForSQL($nr);

		$query = "SELECT `{$this->sutartys_lentele}`.`nr`,
					  `{$this->sutartys_lentele}`.`sutarties_data`,
					  `{$this->sutartys_lentele}`.`nuomos_data_laikas`,
					  `{$this->sutartys_lentele}`.`planuojama_grazinimo_data_laikas`,
					  `{$this->sutartys_lentele}`.`faktine_grazinimo_data_laikas`,
					  `{$this->sutartys_lentele}`.`pradine_rida`,
					  `{$this->sutartys_lentele}`.`galine_rida`,
					  `{$this->sutartys_lentele}`.`kaina`,
					  `{$this->sutartys_lentele}`.`degalu_kiekis_paimant`,
					  `{$this->sutartys_lentele}`.`dagalu_kiekis_grazinus`,
					  `{$this->sutartys_lentele}`.`busena`,
					  `{$this->sutartys_lentele}`.`fk_klientas`,
					  `{$this->sutartys_lentele}`.`fk_darbuotojas`,
					  `{$this->sutartys_lentele}`.`fk_automobilis`,
					  `{$this->sutartys_lentele}`.`fk_grazinimo_vieta`,
					  `{$this->sutartys_lentele}`.`fk_paemimo_vieta`,
					  (IFNULL(SUM(`{$this->uzsakytos_paslaugos_lentele}`.`kaina` * `{$this->uzsakytos_paslaugos_lentele}`.`kiekis`), 0) + `{$this->sutartys_lentele}`.`kaina`) AS `bendra_kaina`
				FROM `{$this->sutartys_lentele}`
					LEFT JOIN `{$this->uzsakytos_paslaugos_lentele}`
						ON `{$this->sutartys_lentele}`.`nr`=`{$this->uzsakytos_paslaugos_lentele}`.`fk_sutartis`
				WHERE `{$this->sutartys_lentele}`.`nr`='{$nr}'
				GROUP BY `{$this->sutartys_lentele}`.`nr`";
		$data = mysql::select($query);

		//
		return $data[0];
	}
	
	/**
	 * Patikrinama, ar sutartis su nurodytu numeriu egzistuoja
	 * @param type $nr
	 * @return type
	 */
	public function checkIfContractNrExists($nr) {
		$nr = mysql::escapeFieldForSQL($nr);

		$query = "SELECT COUNT(`{$this->sutartys_lentele}`.`nr`) AS `kiekis`
				FROM `{$this->sutartys_lentele}`
				WHERE `{$this->sutartys_lentele}`.`nr`='{$nr}'";
		$data = mysql::select($query);

		//
		return $data[0]['kiekis'];
	}

	/**
	 * Užsakytų papildomų paslaugų sąrašo išrinkimas
	 * @param type $contractId
	 * @return type
	 */
	public function getOrderedServices($contractId) {
		$contractId = mysql::escapeFieldForSQL($contractId);

		$query = "SELECT `{$this->uzsakytos_paslaugos_lentele}`.`fk_sutartis`,
					  `{$this->uzsakytos_paslaugos_lentele}`.`fk_kaina_galioja_nuo`,
					  `{$this->uzsakytos_paslaugos_lentele}`.`fk_paslauga`,
					  `{$this->uzsakytos_paslaugos_lentele}`.`kiekis`,
					  `{$this->uzsakytos_paslaugos_lentele}`.`kaina`,
					  `{$this->paslaugos_lentele}`.`pavadinimas`
				FROM `{$this->uzsakytos_paslaugos_lentele}`
					LEFT JOIN `{$this->paslaugos_lentele}`
						ON `{$this->uzsakytos_paslaugos_lentele}`.`fk_paslauga`=`{$this->paslaugos_lentele}`.`id`
				WHERE `fk_sutartis`='{$contractId}'";
		$data = mysql::select($query);
		
		//
		return $data;
	}
	
	/**
	 * Užsakytų papildomų paslaugų sąrašo išrinkimas
	 * @param type $orderId
	 * @return type
	 */
	public function checkIfOrderedServiceExists($contractId, $serviceId, $priceFrom) {
		$query = "SELECT COUNT(`{$this->uzsakytos_paslaugos_lentele}`.`fk_sutartis`) AS `kiekis`
				FROM `{$this->uzsakytos_paslaugos_lentele}`
				WHERE `fk_sutartis`='{$contractId}' AND `fk_paslauga`='{$serviceId}' AND `fk_kaina_galioja_nuo`='{$priceFrom}'";
		$data = mysql::select($query);
	
		//
		return $data[0]['kiekis'];
	}


	/**
	 * Sutarties atnaujinimas
	 * @param type $data
	 */
	public function updateContract($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);

		$query = "UPDATE `{$this->sutartys_lentele}`
				SET `sutarties_data`='{$data['sutarties_data']}',
					`nuomos_data_laikas`='{$data['nuomos_data_laikas']}',
					`planuojama_grazinimo_data_laikas`='{$data['planuojama_grazinimo_data_laikas']}',
					`faktine_grazinimo_data_laikas`='{$data['faktine_grazinimo_data_laikas']}',
					`pradine_rida`='{$data['pradine_rida']}',
					`galine_rida`='{$data['galine_rida']}',
					`kaina`='{$data['kaina']}',
					`degalu_kiekis_paimant`='{$data['degalu_kiekis_paimant']}',
					`dagalu_kiekis_grazinus`='{$data['dagalu_kiekis_grazinus']}',
					`busena`='{$data['busena']}',
					`fk_klientas`='{$data['fk_klientas']}',
					`fk_darbuotojas`='{$data['fk_darbuotojas']}',
					`fk_automobilis`='{$data['fk_automobilis']}',
					`fk_grazinimo_vieta`='{$data['fk_grazinimo_vieta']}',
					`fk_paemimo_vieta`='{$data['fk_paemimo_vieta']}'
				WHERE `nr`='{$data['nr']}'";
		mysql::query($query);
	}
	
	/**
	 * Sutarties įrašymas
	 * @param type $data
	 */
	public function insertContract($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);

		$query = "INSERT INTO `{$this->sutartys_lentele}`
						  (`nr`,
						   `sutarties_data`,
						   `nuomos_data_laikas`,
						   `planuojama_grazinimo_data_laikas`,
						   `faktine_grazinimo_data_laikas`,
						   `pradine_rida`,
						   `galine_rida`,
						   `kaina`,
						   `degalu_kiekis_paimant`,
						   `dagalu_kiekis_grazinus`,
						   `busena`,
						   `fk_klientas`,
						   `fk_darbuotojas`,
						   `fk_automobilis`,
						   `fk_grazinimo_vieta`,
						   `fk_paemimo_vieta`)
				VALUES      ('{$data['nr']}',
						   '{$data['sutarties_data']}',
						   '{$data['nuomos_data_laikas']}',
						   '{$data['planuojama_grazinimo_data_laikas']}',
						   '{$data['faktine_grazinimo_data_laikas']}',
						   '{$data['pradine_rida']}',
						   '{$data['galine_rida']}',
						   '{$data['kaina']}',
						   '{$data['degalu_kiekis_paimant']}',
						   '{$data['dagalu_kiekis_grazinus']}',
						   '{$data['busena']}',
						   '{$data['fk_klientas']}',
						   '{$data['fk_darbuotojas']}',
						   '{$data['fk_automobilis']}',
						   '{$data['fk_grazinimo_vieta']}',
						   '{$data['fk_paemimo_vieta']}')";
		mysql::query($query);
	}
	
	/**
	 * Sutarties šalinimas
	 * @param type $id
	 */
	public function deleteContract($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "DELETE FROM `{$this->sutartys_lentele}`
				WHERE `nr`='{$id}'";
		mysql::query($query);
	}
	
	/**
	 * Visų sutarties užsakytų papildomų paslaugų šalinimas
	 * @param type $contractId
	 */
	public function deleteOrderedServices($contractId) {
		$contractId = mysql::escapeFieldForSQL($contractId);

		$query = "DELETE FROM `{$this->uzsakytos_paslaugos_lentele}`
				WHERE `fk_sutartis`='{$contractId}'";
		mysql::query($query);
	}
	
	/**
	 * Sutarties užsakytos papildomos paslaugos šalinimas
	 * @param type $contractId
	 */
	public function deleteOrderedService($contractId, $serviceId, $priceFrom) {
		$contractId = mysql::escapeFieldForSQL($contractId);
		$serviceId = mysql::escapeFieldForSQL($serviceId);
		$priceFrom = mysql::escapeFieldForSQL($priceFrom);
		//$price = mysql::escapeFieldForSQL($price);

		$query = "DELETE FROM `{$this->uzsakytos_paslaugos_lentele}`
				WHERE `fk_sutartis`='{$contractId}' AND `fk_paslauga`='{$serviceId}' AND `fk_kaina_galioja_nuo`='{$priceFrom}'";
		mysql::query($query);
	}

	/**
	 * Užsakytos papildomos paslaugos atnaujinimas
	 * @param type $data
	 */
	public function updateOrderedService($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);

		$query = "UPDATE `{$this->uzsakytos_paslaugos_lentele}`
				SET `kaina`='{$data['kaina']}',
				    `kiekis`='{$data['kiekis']}'
				WHERE `fk_sutartis`='{$data['fk_sutartis']}' AND `fk_kaina_galioja_nuo`='{$data['fk_kaina_galioja_nuo']}' AND `fk_paslauga`='{$data['fk_paslauga']}'";
		mysql::query($query);
	}
	
	/**
	 * Užsakytos papildomos paslaugos įrašymas
	 * @param type $data
	 */
	public function insertOrderedService($contractId, $serviceId, $priceFrom, $price, $amount) {
		$contractId = mysql::escapeFieldForSQL($contractId);
		$serviceId = mysql::escapeFieldForSQL($serviceId);
		$priceFrom = mysql::escapeFieldForSQL($priceFrom);
		$price = mysql::escapeFieldForSQL($price);
		$amount = mysql::escapeFieldForSQL($amount);

		$query = "INSERT INTO `{$this->uzsakytos_paslaugos_lentele}`
						  (`fk_sutartis`,
						   `fk_kaina_galioja_nuo`,
						   `fk_paslauga`,
						   `kiekis`,
						   `kaina`)
				VALUES	  ('{$contractId}',
						   '{$priceFrom}',
						   '{$serviceId}',
						   '{$amount}',
						   '{$price}')";
		mysql::query($query);
	}


	/**
	 * Sutarties būsenų sąrašo išrinkimas
	 * @return type
	 */
	public function getContractStates() {
		$query = "SELECT *
				FROM `{$this->sutarties_busenos_lentele}`";
		$data = mysql::select($query);
		
		//
		return $data;
	}
	
	/**
	 * Aikštelių sąrašo išrinkimas
	 * @return type
	 */
	public function getParkingLots() {
		$query = "SELECT *
				FROM `{$this->aiksteles_lentele}`";
		$data = mysql::select($query);
		
		//
		return $data;
	}
	
	/**
	 * Paslaugos kainų įtraukimo į užsakymus kiekio radimas
	 * @param type $serviceId
	 * @param type $validFrom
	 * @return type
	 */
	public function getPricesCountOfOrderedServices($serviceId, $validFrom) {
		$serviceId = mysql::escapeFieldForSQL($serviceId);
		$validFrom = mysql::escapeFieldForSQL($validFrom);
		
		$query = "SELECT COUNT(`{$this->uzsakytos_paslaugos_lentele}`.`fk_paslauga`) AS `kiekis`
				FROM `{$this->paslaugu_kainos_lentele}`
					INNER JOIN `{$this->uzsakytos_paslaugos_lentele}`
						ON `{$this->paslaugu_kainos_lentele}`.`fk_paslauga`=`{$this->uzsakytos_paslaugos_lentele}`.`fk_paslauga` AND `{$this->paslaugu_kainos_lentele}`.`galioja_nuo`=`{$this->uzsakytos_paslaugos_lentele}`.`fk_kaina_galioja_nuo`
				WHERE `{$this->paslaugu_kainos_lentele}`.`fk_paslauga`='{$serviceId}' AND `{$this->paslaugu_kainos_lentele}`.`galioja_nuo`='{$validFrom}'";
		$data = mysql::select($query);
		
		//
		return $data[0]['kiekis'];
	}

	/**
	 * Klientų sutarčių sąrašo išrinkimas. Suskaičiuojamos kiekvieno kliento sutarčių ir užsakytų papildomų paslaugų sumos
	 * @param $dateFrom laikotarpio pradžios data
	 * @param $dateTo laikotarpio pabaigos data
	 * @return klientų sutarčių įrašai
	 */
	public function getCustomerContracts($dateFrom, $dateTo) {
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
		
		$query = "SELECT `{$this->sutartys_lentele}`.`nr`,
					  `{$this->sutartys_lentele}`.`sutarties_data`,
					  `{$this->klientai_lentele}`.`asmens_kodas`,
					  `{$this->klientai_lentele}`.`vardas`,
					  `{$this->klientai_lentele}`.`pavarde`,
					  `{$this->sutartys_lentele}`.`kaina` as `sutarties_kaina`,
					  IFNULL(SUM(`{$this->uzsakytos_paslaugos_lentele}`.`kiekis` * `{$this->uzsakytos_paslaugos_lentele}`.`kaina`), 0) as `sutarties_paslaugu_kaina`,
					  `t`.`bendra_kliento_sutarciu_kaina`,
					  `s`.`bendra_kliento_paslaugu_kaina`
				FROM `{$this->sutartys_lentele}`
					INNER JOIN `{$this->klientai_lentele}`
						ON `{$this->sutartys_lentele}`.`fk_klientas`=`{$this->klientai_lentele}`.`asmens_kodas`
					LEFT JOIN `{$this->uzsakytos_paslaugos_lentele}`
						ON `{$this->sutartys_lentele}`.`nr`=`{$this->uzsakytos_paslaugos_lentele}`.`fk_sutartis`
					INNER JOIN (
						SELECT `asmens_kodas`,
							  SUM(`{$this->sutartys_lentele}`.`kaina`) AS `bendra_kliento_sutarciu_kaina`
						FROM `{$this->sutartys_lentele}`
							INNER JOIN `{$this->klientai_lentele}`
								ON `{$this->sutartys_lentele}`.`fk_klientas`=`{$this->klientai_lentele}`.`asmens_kodas`
						{$whereClauseString}
						GROUP BY `asmens_kodas`
					) `t` ON `t`.`asmens_kodas`=`{$this->klientai_lentele}`.`asmens_kodas`
					INNER JOIN (
						SELECT `asmens_kodas`,
							IFNULL(SUM(`{$this->uzsakytos_paslaugos_lentele}`.`kiekis` * `{$this->uzsakytos_paslaugos_lentele}`.`kaina`), 0) as `bendra_kliento_paslaugu_kaina`
						FROM `{$this->sutartys_lentele}`
							INNER JOIN `{$this->klientai_lentele}`
								ON `{$this->sutartys_lentele}`.`fk_klientas`=`{$this->klientai_lentele}`.`asmens_kodas`
							LEFT JOIN `{$this->uzsakytos_paslaugos_lentele}`
								ON `{$this->sutartys_lentele}`.`nr`=`{$this->uzsakytos_paslaugos_lentele}`.`fk_sutartis`
						{$whereClauseString}							
						GROUP BY `asmens_kodas`
					) `s` ON `s`.`asmens_kodas`=`{$this->klientai_lentele}`.`asmens_kodas`
				{$whereClauseString}
				GROUP BY `{$this->sutartys_lentele}`.`nr`
				ORDER BY `{$this->klientai_lentele}`.`pavarde` ASC";
		$data = mysql::select($query);

		//
		return $data;
	}
	
	/**
	 * Sutarčių sumos išrinkimas
	 * @param $dateFrom laikotarpio pradžios data
	 * @param $dateTo laikotarpio pabaigos data
	 * @return įrašas su sutarčių suma
	 */
	public function getSumPriceOfContracts($dateFrom, $dateTo) {
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
		
		$query = "SELECT SUM(`{$this->sutartys_lentele}`.`kaina`) AS `nuomos_suma`
				FROM `{$this->sutartys_lentele}`
				{$whereClauseString}";
		$data = mysql::select($query);

		//
		return $data;
	}

	/**
	 * Užsakytų paslaugų kiekio ir sumos išrinkimas
	 * @param $dateFrom laikotarpio pradžios data
	 * @param $dateTo laikotarpio pabaigos data
	 * @return įrašas su užsakytų paslaugų kiekiu ir suma
	 */
	public function getSumPriceOfOrderedServices($dateFrom, $dateTo) {
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
		
		$query = "SELECT SUM(`{$this->uzsakytos_paslaugos_lentele}`.`kiekis` * `{$this->uzsakytos_paslaugos_lentele}`.`kaina`) AS `paslaugu_suma`
					FROM `{$this->sutartys_lentele}`
						INNER JOIN `{$this->uzsakytos_paslaugos_lentele}`
							ON `{$this->sutartys_lentele}`.`nr`=`{$this->uzsakytos_paslaugos_lentele}`.`fk_sutartis`
				{$whereClauseString}";
		$data = mysql::select($query);

		//
		return $data;
	}
	
	/**
	 * Sutarčių, kuriose automobiliai negrąžinti ar grąžinti pavėluotai, išrinkimas
	 * @param $dateFrom laikotarpio pradžios data
	 * @param $dateTo laikotarpio pabaigos data
	 * @return sutarčių įrašai
	 */
	public function getDelayedCars($dateFrom, $dateTo) {
		$dateFrom = mysql::escapeFieldForSQL($dateFrom);
		$dateTo = mysql::escapeFieldForSQL($dateTo);

		$whereClauseString = "";
		if(!empty($dateFrom)) {
			$whereClauseString .= " AND `{$this->sutartys_lentele}`.`sutarties_data`>='{$dateFrom}'";
			if(!empty($dateTo)) {
				$whereClauseString .= " AND `{$this->sutartys_lentele}`.`sutarties_data`<='{$dateTo}'";
			}
		} else {
			if(!empty($dateTo)) {
				$whereClauseString .= " AND `{$this->sutartys_lentele}`.`sutarties_data`<='{$dateTo}'";
			}
		}
		
		$query = "SELECT `nr`,
					  `sutarties_data`,
					  `planuojama_grazinimo_data_laikas`,
					  IF(`faktine_grazinimo_data_laikas`='0000-00-00 00:00:00', 'negrąžinta', `faktine_grazinimo_data_laikas`) AS `grazinta`,
					  `{$this->klientai_lentele}`.`vardas`,
					  `{$this->klientai_lentele}`.`pavarde`
				FROM `{$this->sutartys_lentele}`
					INNER JOIN `{$this->klientai_lentele}`
						ON `{$this->sutartys_lentele}`.`fk_klientas`=`{$this->klientai_lentele}`.`asmens_kodas`
				WHERE (DATEDIFF(`faktine_grazinimo_data_laikas`, `planuojama_grazinimo_data_laikas`) >= 1 OR
					(`faktine_grazinimo_data_laikas` = '0000-00-00 00:00:00' AND DATEDIFF(NOW(), `planuojama_grazinimo_data_laikas`) >= 1))
					{$whereClauseString}";
		$data = mysql::select($query);

		//
		return $data;
	}
	
}