<?php
/**
 * Sandėlio duomenų valdymo klasė
 *
 * @author YourName
 */

class Warehouse {
    
    private $warehouse_table = '';
    private $product_table = '';

    private $inventorized_product_table = '';
    
    public function __construct() {
        $this->warehouse_table = config::DB_PREFIX . 'sandeliai';
        $this->product_table = config::DB_PREFIX . 'prekes';
        $this->inventorized_product_table = config::DB_PREFIX . 'inventorius';
    }
    
    /**
     * Sandėlio išrinkimas
     * @param type $id
     * @return type
     */
    public function getSandelis($id) {
        $id = mysql::escapeFieldForSQL($id);

        $query = "SELECT *
                FROM `{$this->warehouse_table}`
                WHERE `id`='{$id}'";
        $data = mysql::select($query);
        
        return $data[0];
    }
    
    /**
     * Sandėlių sąrašo išrinkimas
     * @param type $limit
     * @param type $offset
     * @return type
     */
    public function getSandeliaiList($limit = null, $offset = null) {
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
                FROM `{$this->warehouse_table}`
                {$limitOffsetString}";
        $data = mysql::select($query);
        
        return $data;
    }

    /**
     * Sandėlių kiekio radimas
     * @return type
     */
    public function getSandeliaiListCount() {
        $query = "SELECT COUNT(`id`) as `kiekis`
                FROM `{$this->warehouse_table}`";
        $data = mysql::select($query);
        
        return $data[0]['kiekis'];
    }
    
    /**
     * Prekių išrinkimas pagal sandėlį
     * @param type $sandelioId
     * @return type
     */
    public function getPrekesListBySandelis($sandelioId) {
        $sandelioId = mysql::escapeFieldForSQL($sandelioId);

        $query = "SELECT *
                FROM `{$this->product_table}`
                WHERE `fk_sandelis`='{$sandelioId}'";
        $data = mysql::select($query);
        
        return $data;
    }
    
    public function insertWarehouseProduct($data)
	{
		$data = mysql::escapeFieldsArrayForSQL($data);
		
		$checkQuery = "SELECT `id_SANDELIUOJAMA_PREKE` FROM `{$this->inventorized_product_table}`
                  WHERE `fk_PREKEid`='{$data['fk_PREKEid']}' AND `fk_SANDELISsandelio_id`='{$data['fk_SANDELISsandelio_id']}'";
		$existingRecords = mysql::select($checkQuery);

		if (!empty($existingRecords)) {
			// If product already exists in this warehouse, update the quantity
			$existingId = $existingRecords[0]['id_SANDELIUOJAMA_PREKE'];

			$updateQuery = "UPDATE `{$this->inventorized_product_table}`
                       SET `kiekis` = `kiekis` + {$data['kiekis']} 
                       WHERE `id_SANDELIUOJAMA_PREKE`='{$existingId}'";

			mysql::query($updateQuery);
		} else {
			// If product doesn't exist in this warehouse, create a new record
			$insertQuery = "INSERT INTO `{$this->inventorized_product_table}`
                      (`kiekis`,
                       `fk_SANDELISsandelio_id`,
                       `fk_PREKEid`)
                VALUES ('{$data['kiekis']}',
                       '{$data['fk_SANDELISsandelio_id']}',
                       '{$data['fk_PREKEid']}')";

			mysql::query($insertQuery);
		}
	}

    /**
     * Sandėlio atnaujinimas
     * @param type $data
     */
    public function updateSandelis($data) {
        $data = mysql::escapeFieldsArrayForSQL($data);
        
        $query = "UPDATE `{$this->warehouse_table}`
                SET `pavadinimas`='{$data['pavadinimas']}',
                    `adresas`='{$data['adresas']}',
                    `miestas`='{$data['miestas']}',
                    `plotas`='{$data['plotas']}',
                    `talpa`='{$data['talpa']}'
                WHERE `id`='{$data['id']}'";
        mysql::query($query);
    }
    
    /**
     * Sandėlio įrašymas
     * @param type $data
     */
    public function insertSandelis($data) {
        $data = mysql::escapeFieldsArrayForSQL($data);

        $query = "INSERT INTO `{$this->warehouse_table}`
                          (`pavadinimas`,
                           `adresas`,
                           `miestas`,
                           `plotas`,
                           `talpa`)
                VALUES      ('{$data['pavadinimas']}',
                           '{$data['adresas']}',
                           '{$data['miestas']}',
                           '{$data['plotas']}',
                           '{$data['talpa']}')";
        mysql::query($query);
    }
    
    /**
     * Sandėlio šalinimas
     * @param type $id
     */
    public function deleteSandelis($id) {
        $id = mysql::escapeFieldForSQL($id);

        $query = "DELETE FROM `{$this->warehouse_table}`
                WHERE `id`='{$id}'";
        mysql::query($query);
    }
    
    /**
     * Nurodyto sandėlio prekių kiekio radimas
     * @param type $id
     * @return type
     */
    public function getPrekiuCountOfSandelis($id) {
        $id = mysql::escapeFieldForSQL($id);

        $query = "SELECT COUNT(`{$this->product_table}`.`id`) AS `kiekis`
                FROM `{$this->warehouse_table}`
                    INNER JOIN `{$this->product_table}`
                        ON `{$this->warehouse_table}`.`id`=`{$this->product_table}`.`fk_sandelis`
                WHERE `{$this->warehouse_table}`.`id`='{$id}'";
        $data = mysql::select($query);
        
        return $data[0]['kiekis'];
    }
}