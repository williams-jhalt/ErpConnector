<?php

namespace App\Repository;

use App\Model\Customer;
use App\Model\Product;
use App\Service\ErpOneConnector;

/**
 * Service to retrieve products from the database
 */
class CustomerRepository {

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

        $query = "FOR EACH customer NO-LOCK "
                . "WHERE customer.company_cu = '" . $company . "'";

        $fields = "customer.customer,"
                . "customer.name,"
                . "customer.atn_first_name,"
                . "customer.atn_last_name,"
                . "customer.atn_suffix,"
                . "customer.adr,"
                . "customer.state,"
                . "customer.postal_code,"
                . "customer.country_code,"
                . "customer.phone,"
                . "customer.phone_ext,"
                . "customer.fax,"
                . "customer.cell_phone,"
                . "customer.salesman1,"
                . "customer.email_address";

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

        $query = "FOR EACH customer NO-LOCK "
                . "WHERE customer.company_cu = '{$company}' "
                . "AND customer.sy_lookup MATCHES '*{$searchTerms}*'";

        $fields = "customer.customer,"
                . "customer.name,"
                . "customer.atn_first_name,"
                . "customer.atn_last_name,"
                . "customer.atn_suffix,"
                . "customer.adr,"
                . "customer.state,"
                . "customer.postal_code,"
                . "customer.country_code,"
                . "customer.phone,"
                . "customer.phone_ext,"
                . "customer.fax,"
                . "customer.cell_phone,"
                . "customer.salesman1,"
                . "customer.email_address";

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
     * @param string $customerNumber
     * @return Product
     */
    public function getItem(string $company, string $customerNumber) {

        $query = "FOR EACH customer NO-LOCK "
                . "WHERE customer.company_cu = '{$company}' "
                . "AND customer.customer EQ '{$customerNumber}'";

        $fields = "customer.customer,"
                . "customer.name,"
                . "customer.atn_first_name,"
                . "customer.atn_last_name,"
                . "customer.atn_suffix,"
                . "customer.adr,"
                . "customer.state,"
                . "customer.postal_code,"
                . "customer.country_code,"
                . "customer.phone,"
                . "customer.phone_ext,"
                . "customer.fax,"
                . "customer.cell_phone,"
                . "customer.salesman1,"
                . "customer.email_address";

        $response = $this->service->read($company, $query, $fields, 1);

        if (sizeof($response) == 0) {
            return null;
        }
        
        return $this->_buildFromErp($response[0]);
        
    }
    
    private function _buildFromErp($erpItem) {
        
        $t = new Customer();
        $t->setCustomerNumber($erpItem->customer_customer);
        $t->setName($erpItem->customer_name);
        $t->setAttentionFirstName($erpItem->customer_atn_first_name);
        $t->setAttentionLastName($erpItem->customer_atn_last_name);
        $t->setAttentionSuffix($erpItem->customer_atn_suffix);
        $t->setBillToAddress1($erpItem->customer_adr[0]);
        $t->setBillToAddress2($erpItem->customer_adr[1]);
        $t->setBillToAddress3($erpItem->customer_adr[2]);
        $t->setBillToCity($erpItem->customer_adr[3]);
        $t->setBillToState($erpItem->customer_state);
        $t->setBillToPostalCode($erpItem->customer_postal_code);
        $t->setBillToCountry($erpItem->customer_country_code);
        $t->setBillToPhone($erpItem->customer_phone);
        $t->setBillToPhoneExt($erpItem->customer_phone_ext);
        $t->setBillToFax($erpItem->customer_fax);
        $t->setBillToCellPhone($erpItem->customer_cell_phone);
        $t->setSalespersonId($erpItem->customer_salesman1);
        $t->setEmail($erpItem->customer_email_address);
        
        return $t;
        
    }

}
