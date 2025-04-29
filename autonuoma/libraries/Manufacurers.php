<?php
/**
 * Juvelyrikos gamintojų redagavimo klasė
 *
 * @author ISK
 */

class manufacturers {
	
	private $manufacturer_table = '';
	private $product_table = '';
	
	public function __construct() {
		$this->manufacturer_table = config::DB_PREFIX . 'gamintojas';
		$this->product_table = config::DB_PREFIX . 'preke';
	}
	
	/**
	 * Gamintojo išrinkimas
	 * @param type $id
	 * @return type
	 */
	public function getManufacturer($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "SELECT *
				FROM `{$this->manufacturer_table}`
				WHERE `gamintojo_id`='{$id}'";
		$data = mysql::select($query);
		
		//
		return $data[0];
	}
	
	/**
	 * Gamintojų sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getManufacturerList($limit = null, $offset = null) {
		if($limit) {
			$limit = mysql::escapeFieldForSQL($limit);
		}
		if($offset) {
			$offset = mysql::escapeFieldForSQL($offset);
		}

		$limitOffsetString = "";
		if(isset($limit)) {
			$limitOffsetString .= " LIMIT {$limit}";
			
			if(isset($offset)) {
				$limitOffsetString .= " OFFSET {$offset}";
			}	
		}
		
		$query = "SELECT *
				FROM `{$this->manufacturer_table}`
				{$limitOffsetString}";
		$data = mysql::select($query);
		
		//
		return $data;
	}

	/**
	 * Gamintojų kiekio radimas
	 * @return type
	 */
	public function getManufacturerListCount() {
		$query = "SELECT COUNT(`gamintojo_id`) as `kiekis`
				FROM `{$this->manufacturer_table}`";
		$data = mysql::select($query);
		
		// 
		return $data[0]['kiekis'];
	}
	
	/**
	 * Gamintojo įrašymas
	 * @param type $data
	 */
	public function insertManufacturer($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);

		$query = "INSERT INTO `{$this->manufacturer_table}`
						  (`gamintojo_id`,
						   `pavadinimas`,
						   `salis`,
						   `kontaktai`)
				VALUES      ('{$data['gamintojo_id']}',
						  '{$data['pavadinimas']}',
						  '{$data['salis']}',
						  '{$data['kontaktai']}')";
		mysql::query($query);
	}
	
	/**
	 * Gamintojo atnaujinimas
	 * @param type $data
	 */
	public function updateManufacturer($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);

		$query = "UPDATE `{$this->manufacturer_table}`
				SET `pavadinimas`='{$data['pavadinimas']}',
				    `salis`='{$data['salis']}',
				    `kontaktai`='{$data['kontaktai']}'
				WHERE `gamintojo_id`='{$data['gamintojo_id']}'";
		mysql::query($query);
	}
	
	/**
	 * Gamintojo šalinimas
	 * @param type $id
	 */
	public function deleteManufacturer($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "DELETE FROM `{$this->manufacturer_table}`
				WHERE `gamintojo_id`='{$id}'";
		mysql::query($query);
	}
	
	/**
	 * Gamintojo prekių kiekio radimas
	 * @param type $id
	 * @return type
	 */
	public function getProductCountOfManufacturer($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "SELECT COUNT(`{$this->product_table}`.`id`) AS `kiekis`
				FROM `{$this->manufacturer_table}`
					INNER JOIN `{$this->product_table}`
						ON `{$this->manufacturer_table}`.`gamintojo_id`=`{$this->product_table}`.`fk_GAMINTOJASgamintojo_id`
				WHERE `{$this->manufacturer_table}`.`gamintojo_id`='{$id}'";
		$data = mysql::select($query);
		
		//
		return $data[0]['kiekis'];
	}
}