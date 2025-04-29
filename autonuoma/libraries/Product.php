<?php
/**
 * Juvelyrikos prekių redagavimo klasė
 *
 * @author ISK
 */

class product {

	private $category_table = '';
	private $product_table = '';
	private $manufacturer_table = '';
	private $review_table = '';
	private $inventorized_product_table = '';
	private $warehouse_table = '';
	private $order_product_table = '';
	private $order_table = '';
	
	public function __construct() {
		$this->category_table = config::DB_PREFIX . 'kategorija';
		$this->product_table = config::DB_PREFIX . 'preke';
		$this->manufacturer_table = config::DB_PREFIX . 'gamintojas';
		$this->review_table = config::DB_PREFIX . 'atsiliepimas';
		$this->inventorized_product_table = config::DB_PREFIX . 'sandeliuojama_preke';
		$this->warehouse_table = config::DB_PREFIX . 'sandelis';
		$this->order_product_table = config::DB_PREFIX . 'uzsakymo_preke';		
		$this->order_table = config::DB_PREFIX . 'uzsakymas';
	}
	
	/**
	 * Automobilio išrinkimas
	 * @param type $id
	 * @return type
	 */
	public function getProduct($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "SELECT `{$this->product_table}`.`id`,
					  `{$this->product_table}`.`pavadinimas`,
					  `{$this->product_table}`.`aprasymas`,
					  `{$this->product_table}`.`kaina`,
					  `{$this->product_table}`.`svoris`,
					  `{$this->product_table}`.`medziaga`,
					  `{$this->product_table}`.`fk_GAMINTOJASgamintojo_id`,
					  `{$this->product_table}`.`fk_KATEGORIJAid_KATEGORIJA`,
				FROM `{$this->product_table}`
				WHERE `{$this->product_table}`.`id`='{$id}'";
		$data = mysql::select($query);
		
		//
		return $data[0];
	}
	
	/**
	 * Automobilio atnaujinimas
	 * @param type $data
	 */
	public function updateProduct($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);

		$query = "UPDATE `{$this->product_table}`
				SET `pavadinimas`='{$data['pavadinimas']}',
 					`aprasymas`='{$data['aprasymas']}',
				    `kaina`='{$data['kaina']}',
				    `svoris`='{$data['svoris']}',
				    `medziaga`='{$data['medziaga']}',
				    `fk_GAMINTOJASgamintojo_id`='{$data['fk_GAMINTOJASgamintojo_id']}',
				    `fk_KATEGORIJAid_KATEGORIJA`='{$data['fk_KATEGORIJAid_KATEGORIJA']}'
				WHERE `id`='{$data['id']}'";
		mysql::query($query);
	}

	/**
	 * Automobilio įrašymas
	 * @param type $data
	 */
	public function insertProduct($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);

		$query = "INSERT INTO `{$this->product_table}` 
						  (`id`,
						   `pavadinimas`,
						   `aprasymas`,
						   `kaina`,
						   `svoris`,
						   `medziaga`,
						   `fk_GAMINTOJASgamintojo_id`,
						   `fk_KATEGORIJAid_KATEGORIJA`) 
				VALUES	  ('{$data['id']}',
						   '{$data['pavadinimas']}',
						   '{$data['aprasymas']}',
						   '{$data['kaina']}',
						   '{$data['svoris']}',
						   '{$data['medziaga']}',
						   '{$data['fk_GAMINTOJASgamintojo_id']}',
						   '{$data['fk_KATEGORIJAid_KATEGORIJA']}')";
		mysql::query($query);
	}
	
	/**
	 * Automobilių sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getProductList($limit = null, $offset = null) {
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
		
		$query = "SELECT `{$this->product_table}`.`id`,
					  `{$this->product_table}`.`pavadinimas`,
					  `{$this->product_table}`.`kaina`,
					  `{$this->product_table}`.`svoris`,
					  `{$this->category_table}`.`pavadinimas` AS `kategorija`,
					  `{$this->manufacturer_table}`.`pavadinimas` AS `gamintojas`
				FROM `{$this->product_table}`
					LEFT JOIN `{$this->category_table}`
						ON `{$this->product_table}`.`fk_KATEGORIJAid_KATEGORIJA`=`{$this->category_table}`.`id_KATEGORIJA`
					LEFT JOIN `{$this->manufacturer_table}`
						ON `{$this->product_table}`.`fk_GAMINTOJASgamintojo_id`=`{$this->manufacturer_table}`.`gamintojo_id`" .
				$limitOffsetString;
		$data = mysql::select($query);
		
		//
		return $data;
	}

	/**
	 * Automobilių kiekio radimas
	 * @return type
	 */
	public function getProductListCount() {
		$query = "SELECT COUNT(`{$this->product_table}`.`id`) AS `kiekis`
				FROM `{$this->product_table}`
					LEFT JOIN `{$this->category_table}`
						ON `{$this->product_table}`.`fk_KATEGORIJAid_KATEGORIJA`=`{$this->category_table}`.`id_KATEGORIJA`
					LEFT JOIN `{$this->manufacturer_table}`
						ON `{$this->product_table}`.`fk_GAMINTOJASgamintojo_id`=`{$this->manufacturer_table}`.`gamintojo_id`";
		$data = mysql::select($query);
		
		return $data[0]['kiekis'];
	}
	
	/**
	 * Automobilio šalinimas
	 * @param type $id
	 */
	public function deleteProduct($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "DELETE FROM `{$this->product_table}`
				WHERE `id`='{$id}'";
		mysql::query($query);
	}
	
	/**
	 * Sutačių, į kurias įtrauktas automobilis, kiekio radimas
	 * @param type $id
	 * @return type
	 */
	public function getOrderCountOfProduct($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "SELECT COUNT(`{$this->inventorized_product_table}`.`id_UZSAKYMO_PREKE`) AS `kiekis`
				FROM `{$this->product_table}`
					INNER JOIN `{$this->inventorized_product_table}`
						ON `{$this->product_table}`.`id`=`{$this->inventorized_product_table}`.`fk_PREKEid`
				WHERE `{$this->product_table}`.`id`='{$id}'";
		$data = mysql::select($query);
		
		//
		return $data[0]['kiekis'];
	}
	
	public function getReviewCountOfProduct($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "SELECT COUNT(`{$this->review_table}`.`id_ATSILIEPIMAS`) AS `kiekis`
				FROM `{$this->product_table}`
					INNER JOIN `{$this->review_table}`
						ON `{$this->product_table}`.`id`=`{$this->product_table}`.`fk_PREKEid`
				WHERE `{$this->product_table}`.`id`='{$id}'";
		$data = mysql::select($query);
		
		//
		return $data[0]['kiekis'];
	}

	public function getProductStockCount($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "SELECT SUM(`{$this->inventorized_product_table}`.`kiekis`) AS `kiekis`
				FROM `{$this->product_table}`
					INNER JOIN `{$this->inventorized_product_table}`
						ON `{$this->product_table}`.`id`=`{$this->inventorized_product_table}`.`fk_PREKEid`
				WHERE `{$this->product_table}`.`id`='{$id}'";
		$data = mysql::select($query);
		
		//
		return $data[0]['kiekis'];
	}
	/**
	 * Pavarų dėžių sąrašo išrinkimas
	 * @return type
	 */
	public function getOrderList() {
		$query = "SELECT *
				FROM `{$this->order_table}`";
		$data = mysql::select($query);
		
		//
		return $data;
	}
	
	/**
	 * Degalų tipo sąrašo išrinkimas
	 * @return type
	 */
	public function getWarehouseList() {
		$query = "SELECT *
				FROM `{$this->warehouse_table}`";
		$data = mysql::select($query);
		
		//
		return $data;
	}

	/**
	 * Kėbulo tipų sąrašo išrinkimas
	 * @return type
	 */
	public function getOrderProductList() {
		$query = "SELECT *
				FROM `{$this->order_product_table}`";
		$data = mysql::select($query);
		
		//
		return $data;
	}

	/**
	 * Bagažo tipų sąrašo išrinkimas
	 * @return type
	 */
	public function getCategoryList() {
		$query = "SELECT *
				FROM `{$this->category_table}`";
		$data = mysql::select($query);
		
		//
		return $data;
	}

	/**
	 * Automobilio būsenų sąrašo išrinkimas
	 * @return type
	 */
	public function getReviewList() {
		$query = "SELECT *
				FROM `{$this->review_table}`";
		$data = mysql::select($query);
		
		//
		return $data;
	}
		/**
	 * Patikrinama, ar sutartis su nurodytu numeriu egzistuoja
	 * @param type $id
	 * @return type
	 */
	public function checkIfProductidExists($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "SELECT COUNT(`{$this->product_table}`.`id`) AS `kiekis`
				FROM `{$this->product_table}`
				WHERE `{$this->product_table}`.`id`='{$id}'";
		$data = mysql::select($query);

		//
		return $data[0]['kiekis'];
	}
}