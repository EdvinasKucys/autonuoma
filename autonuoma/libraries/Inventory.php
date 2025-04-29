<?php
/**
 * Juvelyrikos sandėliuojamų prekių redagavimo klasė
 *
 * @author ISK
 */

class inventory {
	
	private $inventorized_product_table = '';
	private $product_table = '';
	private $warehouse_table = '';
	private $category_table = '';
	private $manufacturer_table = '';
	
	public function __construct() {
		$this->inventorized_product_table = config::DB_PREFIX . 'sandeliuojama_preke';
		$this->product_table = config::DB_PREFIX . 'preke';
		$this->warehouse_table = config::DB_PREFIX . 'sandelis';
		$this->category_table = config::DB_PREFIX . 'kategorija';
		$this->manufacturer_table = config::DB_PREFIX . 'gamintojas';
	}
	
	/**
	 * Sandėliuojamos prekės išrinkimas
	 * @param type $id
	 * @return type
	 */
	public function getInventoryItem($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "SELECT `{$this->inventorized_product_table}`.`id_SANDELIUOJAMA_PREKE`,
					  `{$this->inventorized_product_table}`.`kiekis`,
					  `{$this->inventorized_product_table}`.`fk_SANDELISsandelio_id`,
					  `{$this->inventorized_product_table}`.`fk_PREKEid`,
					  `{$this->product_table}`.`pavadinimas` AS `prekes_pavadinimas`,
					  `{$this->warehouse_table}`.`pavadinimas` AS `sandelio_pavadinimas`
				FROM `{$this->inventorized_product_table}`
					INNER JOIN `{$this->product_table}`
						ON `{$this->inventorized_product_table}`.`fk_PREKEid`=`{$this->product_table}`.`id`
					INNER JOIN `{$this->warehouse_table}`
						ON `{$this->inventorized_product_table}`.`fk_SANDELISsandelio_id`=`{$this->warehouse_table}`.`sandelio_id`
				WHERE `{$this->inventorized_product_table}`.`id_SANDELIUOJAMA_PREKE`='{$id}'";
		$data = mysql::select($query);
		
		//
		return $data[0];
	}
	
	/**
	 * Sandėliuojamų prekių sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getInventoryList($limit = null, $offset = null) {
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
		
		$query = "SELECT `{$this->inventorized_product_table}`.`id_SANDELIUOJAMA_PREKE`,
					  `{$this->inventorized_product_table}`.`kiekis`,
					  `{$this->product_table}`.`id` AS `prekes_id`,
					  `{$this->product_table}`.`pavadinimas` AS `prekes_pavadinimas`,
					  `{$this->warehouse_table}`.`sandelio_id`,
					  `{$this->warehouse_table}`.`pavadinimas` AS `sandelio_pavadinimas`,
					  `{$this->category_table}`.`pavadinimas` AS `kategorija`,
					  `{$this->manufacturer_table}`.`pavadinimas` AS `gamintojas`
				FROM `{$this->inventorized_product_table}`
					INNER JOIN `{$this->product_table}`
						ON `{$this->inventorized_product_table}`.`fk_PREKEid`=`{$this->product_table}`.`id`
					INNER JOIN `{$this->warehouse_table}`
						ON `{$this->inventorized_product_table}`.`fk_SANDELISsandelio_id`=`{$this->warehouse_table}`.`sandelio_id`
					INNER JOIN `{$this->category_table}`
						ON `{$this->product_table}`.`fk_KATEGORIJAid_KATEGORIJA`=`{$this->category_table}`.`id_KATEGORIJA`
					INNER JOIN `{$this->manufacturer_table}`
						ON `{$this->product_table}`.`fk_GAMINTOJASgamintojo_id`=`{$this->manufacturer_table}`.`gamintojo_id`
				{$limitOffsetString}";
		$data = mysql::select($query);
		
		//
		return $data;
	}

	/**
	 * Sandėliuojamų prekių kiekio radimas
	 * @return type
	 */
	public function getInventoryListCount() {
		$query = "SELECT COUNT(`{$this->inventorized_product_table}`.`id_SANDELIUOJAMA_PREKE`) as `kiekis`
				FROM `{$this->inventorized_product_table}`";
		$data = mysql::select($query);
		
		// 
		return $data[0]['kiekis'];
	}
	
	/**
	 * Sandėliuojamos prekės įrašymas
	 * @param type $data
	 */
	public function insertInventoryItem($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);

		$query = "INSERT INTO `{$this->inventorized_product_table}`
						  (`kiekis`,
						   `fk_SANDELISsandelio_id`,
						   `fk_PREKEid`)
				VALUES      ('{$data['kiekis']}',
						  '{$data['fk_SANDELISsandelio_id']}',
						  '{$data['fk_PREKEid']}')";
		mysql::query($query);
	}
	
	/**
	 * Sandėliuojamos prekės atnaujinimas
	 * @param type $data
	 */
	public function updateInventoryItem($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);

		$query = "UPDATE `{$this->inventorized_product_table}`
				SET `kiekis`='{$data['kiekis']}',
				    `fk_SANDELISsandelio_id`='{$data['fk_SANDELISsandelio_id']}',
				    `fk_PREKEid`='{$data['fk_PREKEid']}'
				WHERE `id_SANDELIUOJAMA_PREKE`='{$data['id_SANDELIUOJAMA_PREKE']}'";
		mysql::query($query);
	}
	
	/**
	 * Sandėliuojamos prekės šalinimas
	 * @param type $id
	 */
	public function deleteInventoryItem($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "DELETE FROM `{$this->inventorized_product_table}`
				WHERE `id_SANDELIUOJAMA_PREKE`='{$id}'";
		mysql::query($query);
	}
	
	/**
	 * Prekių sąrašo išrinkimas
	 * @return type
	 */
	public function getProductList() {
		$query = "SELECT `id`, `pavadinimas`
				FROM `{$this->product_table}`
				ORDER BY `pavadinimas` ASC";
		$data = mysql::select($query);
		
		//
		return $data;
	}
	
	/**
	 * Sandėlių sąrašo išrinkimas
	 * @return type
	 */
	public function getWarehouseList() {
		$query = "SELECT `sandelio_id`, `pavadinimas`
				FROM `{$this->warehouse_table}`
				ORDER BY `pavadinimas` ASC";
		$data = mysql::select($query);
		
		//
		return $data;
	}
	
	/**
	 * Tam tikros prekės sandėliuojamų vienetų išrinkimas
	 * @param type $productId
	 * @return type
	 */
	public function getProductInventory($productId) {
		$productId = mysql::escapeFieldForSQL($productId);
		
		$query = "SELECT `{$this->inventorized_product_table}`.`id_SANDELIUOJAMA_PREKE`,
					  `{$this->inventorized_product_table}`.`kiekis`,
					  `{$this->inventorized_product_table}`.`fk_SANDELISsandelio_id`,
					  `{$this->warehouse_table}`.`pavadinimas` AS `sandelio_pavadinimas`
				FROM `{$this->inventorized_product_table}`
					INNER JOIN `{$this->warehouse_table}`
						ON `{$this->inventorized_product_table}`.`fk_SANDELISsandelio_id`=`{$this->warehouse_table}`.`sandelio_id`
				WHERE `{$this->inventorized_product_table}`.`fk_PREKEid`='{$productId}'";
		$data = mysql::select($query);
		
		//
		return $data;
	}
	
	/**
	 * Tam tikros prekės bendrų sandėliuojamų vienetų kiekio radimas
	 * @param type $productId
	 * @return type
	 */
	public function getTotalProductCount($productId) {
		$productId = mysql::escapeFieldForSQL($productId);
		
		$query = "SELECT SUM(`kiekis`) AS `viso`
				FROM `{$this->inventorized_product_table}`
				WHERE `fk_PREKEid`='{$productId}'";
		$data = mysql::select($query);
		
		//
		return $data[0]['viso'];
	}
	
	/**
	 * Prekių, kurios yra tam tikrame sandėlyje, kiekio radimas
	 * @param type $warehouseId
	 * @return type
	 */
	public function getProductCountInWarehouse($warehouseId) {
		$warehouseId = mysql::escapeFieldForSQL($warehouseId);
		
		$query = "SELECT COUNT(`id_SANDELIUOJAMA_PREKE`) AS `kiekis`
				FROM `{$this->inventorized_product_table}`
				WHERE `fk_SANDELISsandelio_id`='{$warehouseId}'";
		$data = mysql::select($query);
		
		//
		return $data[0]['kiekis'];
	}
}