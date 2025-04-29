
<?php
/**
 * Prekiu kategoriju redagavimo klasė
 *
 * @author ISK
 */

class brands {
	
	private $category_table = '';
	private $product_table = '';
	
	public function __construct() {
		$this->category_table = config::DB_PREFIX . 'kategorija';
		$this->product_table = config::DB_PREFIX . 'preke';
	}
	
	/**
	 * Markės išrinkimas
	 * @param type $id
	 * @return type
	 */
	public function getCategory($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "SELECT *
				FROM {$this->category_table
		}
				WHERE `id`='{$id}'";
		$data = mysql::select($query);
		
		//
		return $data[0];
	}
	
	/**
	 * Markių sąrašo išrinkimas
	 * @param type $limit
	 * @param type $offset
	 * @return type
	 */
	public function getCategoryList($limit = null, $offset = null) {
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
				FROM {$this->category_table
		}
				{$limitOffsetString}";
		$data = mysql::select($query);
		
		//
		return $data;
	}

	/**
	 * Markių kiekio radimas
	 * @return type
	 */
	public function getCategoryListCount() {
		$query = "SELECT COUNT(`id_KATEGORIJA`) as `kiekis`
				FROM {$this->category_table
		}";
		$data = mysql::select($query);
		
		// 
		return $data[0]['kiekis'];
	}
	
	/**
	 * Markės įrašymas
	 * @param type $data
	 */
	public function insertategory($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);

		$query = "INSERT INTO {$this->category_table
}
						  (`pavadinimas`, 'aprasymas')
				VALUES      ('{$data['pavadinimas']}', '{$data['aprasymas']}')";
		mysql::query($query);
	}
	
	/**
	 * Markės atnaujinimas
	 * @param type $data
	 */
	public function updateBrand($data) {
		$data = mysql::escapeFieldsArrayForSQL($data);

		$query = "UPDATE {$this->category_table
}
				SET 
				`pavadinimas`='{$data['pavadinimas']}',
				`aprasymas`='{$data['aprasymas']}'
				WHERE `id`='{$data['id']}'";
		mysql::query($query);
	}
	
	/**
	 * Markės šalinimas
	 * @param type $id
	 */
	public function deleteBrand($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "DELETE FROM {$this->category_table
}
				WHERE `id_KATEGORIJA`='{$id}'";
		mysql::query($query);
	}
	
	/**
	 * Markės modelių kiekio radimas
	 * @param type $id
	 * @return type
	 */
	public function getProductCountOfCategory($id) {
		$id = mysql::escapeFieldForSQL($id);

		$query = "SELECT COUNT({$this->product_table}.`id`) AS `kiekis`
				FROM {$this->category_table
		}
					INNER JOIN {$this->product_table}
						ON {$this->category_table
				}.`id_KATEGORIJA`={$this->product_table}.`fk_KATEGORIJAid_KATEGORIJA`
				WHERE {$this->category_table
		}.`id`='{$id}'";
		$data = mysql::select($query);
		
		//
		return $data[0]['kiekis'];
	}

	
}