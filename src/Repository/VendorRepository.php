<?php

namespace App\Repository;

use App\Model\Vendor;
use App\Service\ErpOneConnector;

/**
 * Service to retrieve products from the database
 */
class VendorRepository {

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
     * @return Vendor[]
     */
    public function getItems($company, int $limit = 100, int $offset = 0) {

        $query = "FOR EACH vendor NO-LOCK "
                . "WHERE vendor.company_ve = '" . $company . "'";

        $fields = "vendor.vendor,"
                . "vendor.name,"
                . "vendor.email_address";

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
     * @return Vendor[]
     */
    public function searchItems(string $company, string $searchTerms, int $limit = 100, int $offset = 0) {

        $query = "FOR EACH vendor NO-LOCK "
                . "WHERE vendor.company_ve = '{$company}' "
                . "AND vendor.sy_lookup MATCHES '*{$searchTerms}*'";

        $fields = "vendor.vendor,"
                . "vendor.name,"
                . "vendor.email_address";

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
     * @param string $vendorNumber
     * @return Vendor
     */
    public function getItem(string $company, string $vendorNumber) {

        $query = "FOR EACH vendor NO-LOCK "
                . "WHERE vendor.company_ve = '{$company}' "
                . "AND vendor.vendor EQ '{$vendorNumber}'";

        $fields = "vendor.vendor,"
                . "vendor.name,"
                . "vendor.email_address";

        $response = $this->service->read($company, $query, $fields, 1);

        if (sizeof($response) == 0) {
            return null;
        }
        
        return $this->_buildFromErp($response[0]);
        
    }
    
    private function _buildFromErp($erpItem) {
        
        $t = new Vendor();
        $t->setCustomerNumber($erpItem->vendor_vendor);
        $t->setName($erpItem->vendor_name);
        $t->setEmail($erpItem->vendor_email_address);
        
        return $t;
        
    }

}
