<?php

namespace App\Repository;

use App\Model\Product;
use App\Model\SalesPerson;
use App\Service\ErpOneConnector;

/**
 * Service to retrieve products from the database
 */
class SalesPersonRepository {

    /**
     * @var ErpOneConnector
     */
    private $service;

    public function __construct(ErpOneConnector $service) {
        $this->service = $service;
    }

    /**
     * Retrieves all items from the database
     * 
     * @param integer $limit
     * @param integer $offset
     * @return Product[]
     */
    public function getItems($company, int $limit = 100, int $offset = 0) {

        $query = "FOR EACH salesman NO-LOCK "
                . "WHERE salesman.company_cu = '" . $company . "'";

        $fields = "salesman.salesman,"
                . "salesman.name";

        $response = $this->service->read($company, $query, $fields, $limit, $offset);

        $result = array();

        foreach ($response as $erpItem) {
            $result[] = $this->_buildFromErp($erpItem);
        }

        return $result;
    }

    /**
     * Retrieves matching items from the database
     * 
     * @param string company
     * @param string $searchTerms
     * @param integer $limit
     * @param integer $offset
     * @return Product[]
     */
    public function searchItems(string $company, string $searchTerms, int $limit = 100, int $offset = 0) {

        $query = "FOR EACH salesman NO-LOCK "
                . "WHERE salesman.company_cu = '" . $company . "'"
                . "AND salesman.name MATCHES '*{$searchTerms}*'";

        $fields = "salesman.salesman,"
                . "salesman.name";

        $response = $this->service->read($company, $query, $fields, $limit, $offset);

        $result = array();

        foreach ($response as $erpItem) {
            $result[] = $this->_buildFromErp($erpItem);
        }

        return $result;
    }

    /**
     * Retrieves a single product from the database,
     * id can be either the item number or barcode
     * 
     * @param string $id
     * @return Product
     */
    public function getItem(string $company, string $id) {

        $query = "FOR EACH salesman NO-LOCK "
                . "WHERE salesman.company_cu = '" . $company . "'"
                . "AND salesman.salesman EQ '{$id}'";

        $fields = "salesman.salesman,"
                . "salesman.name";

        $response = $this->service->read($company, $query, $fields, 1);

        if (sizeof($response) == 0) {
            return null;
        }
        
        return $this->_buildFromErp($response[0]);
        
    }
    
    private function _buildFromErp($erpItem) {
        
        $t = new SalesPerson();
        $t->setId($erpItem->salesman_salesman);
        $t->setName($erpItem->salesman_name);
        
        return $t;
        
    }

}
