<?php

namespace App\Service;

use Symfony\Component\Cache\Simple\FilesystemCache;

class ErpOneConnector {

    private $_grantToken;
    private $_accessToken;
    private $_server;
    private $_username;
    private $_password;
    private $_grantTime;
    private $_cache;
    private $_cacheId;
    private $_appname;
    private $_company;

    /**
     * 
     * @param string $server
     * @param string $username
     * @param string $password
     * @param string $appname
     */
    public function __construct($server, $username, $password, $appname) {
        $this->_cache = new FilesystemCache();
        $this->_server = $server;
        $this->_username = $username;
        $this->_password = $password;
        $this->_appname = $appname;
    }

    /**
     * Retrieves API token from ERP
     * 
     * @param string $company
     * @throws ErpServiceException
     */
    private function _getGrantToken($company) {

        $this->_cacheId = md5("erp_token:{$this->_server}:{$company}:{$this->_appname}");

        if ($this->_cache->has($this->_cacheId)) {            
            $data = $this->_cache->get($this->_cacheId);
            $this->_grantToken = $data[0];
            $this->_accessToken = $data[1];
            $this->_grantTime = $data[2];
            return;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->_server . "/distone/rest/service/authorize/grant");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'client' => $this->_appname,
            'company' => $company,
            'username' => $this->_username,
            'password' => $this->_password
        )));

        $response = json_decode(curl_exec($ch));

        if (isset($response->_errors)) {
            $this->_cache->delete($this->_cacheId);
            throw new ErpServiceException($response->_errors[0]->_errorMsg, $response->_errors[0]->_errorNum); // find out the structure of ERP-ONE's errors
        }

        $this->_grantToken = $response->grant_token;
        $this->_accessToken = $response->access_token;

        $this->_grantTime = time();

        $this->_cache->set($this->_cacheId, [
            $this->_grantToken,
            $this->_accessToken,
            $this->_grantTime
        ]);

        curl_close($ch);
        
    }

    /**
     * Refreshes API token from ERP if expired
     * 
     * @return null
     */
    private function _refreshToken($company) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->_server . "/distone/rest/service/authorize/access");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'client' => $this->_appname,
            'company' => $company,
            'grant_token' => $this->_grantToken
        )));

        $response = json_decode(curl_exec($ch));

        if (isset($response->_errors)) {
            $this->_cache->delete($this->_cacheId);
            $this->_getGrantToken($company);
        }

        $this->_accessToken = $response->access_token;

        $this->_grantTime = time();

        $this->_cache->set($this->_cacheId, [
            $this->_grantToken,
            $this->_accessToken,
            $this->_grantTime
        ]);

        curl_close($ch);
        
    }

    private function _getAccessToken($company) {
                
        $this->_getGrantToken($company);

        if ($this->_grantTime < (time() - (60 * 3))) {        
            $this->_refreshToken($company);
        }
        
        return $this->_accessToken;
        
    }

    /**
     * 
     * @param string $company
     * @param string $table
     * @param array $records
     * @param boolean $triggers
     * @return mixed
     * @throws ErpServiceException
     */
    public function create($company, $table, $records, $triggers = true) {

        $this->_company = $company;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->_server . "/distone/rest/service/data/create");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: ' . $this->_getAccessToken($company)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $request = json_encode(array(
            'table' => $table,
            'records' => $records,
            'triggers' => $triggers
        ));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        $t = curl_exec($ch);

        $response = json_decode($t);

        curl_close($ch);

        if (isset($response->_errors)) {
            throw new ErpServiceException($response->_errors[0]->_errorMsg, $response->_errors[0]->_errorNum); // find out the structure of ERP-ONE's errors
        }

        return $response;
    }

    /**
     * 
     * @param string $query
     * @param string $columns
     * @param integer $limit
     * @param integer $offset
     * @return mixed
     * @throws ErpServiceException
     */
    public function read($company, $query, $columns = "*", $limit = 0, $offset = 0) {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->_server . "/distone/rest/service/data/read");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: ' . $this->_getAccessToken($company)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'query' => $query,
            'columns' => $columns,
            'skip' => $offset,
            'take' => $limit
        )));

        $response = json_decode(curl_exec($ch));

        curl_close($ch);

        if (isset($response->_errors)) {
            throw new ErpServiceException($response->_errors[0]->_errorMsg, $response->_errors[0]->_errorNum); // find out the structure of ERP-ONE's errors
        }
        
        if (sizeof($response) == 0) {
            throw new ErpServiceException("No items found");
        }

        return $response;
    }

    /**
     * Gets item pricing based on customer, quantity, and unit of measure
     * 
     * Returns a object containing the following:
     * 
     * item - Item Number of the item that the price was calculated for.
     * warehouse - Warehouse Code used in the price calculation.
     * customer - Customer Id used to calculate customer based pricing.
     * cu_group - Customer Group code used in the price calculation.
     * vendor - Vendor Id used in the price calculation.
     * quantity - Quantity used to get the price at a specific quantity break level.
     * price - The calculated price of the item.
     * unit - Unit of measure code (price per).
     * origin - Price calculation origin code. This code indicates how the price was calculated internally.
     * commission - A sales commission percentage for the item.
     * column - Column price label when a column price was used in the calculation.
     * 
     * @param string $company
     * @param string $itemNumber
     * @param string $customer
     * @param integer $quantity
     * @param string $uom
     * @return mixed
     */
    public function getItemPriceDetails($company, $itemNumber, $customer = null, $quantity = 1, $uom = "EA") {

        $ch = curl_init();

        $queryData = array();

        $queryData['item'] = $itemNumber;

        if ($customer !== null) {
            $queryData['customer'] = $customer;
        }

        $queryData['quantity'] = $quantity;
        $queryData['unit'] = $uom;

        $query = http_build_query($queryData);

        curl_setopt($ch, CURLOPT_URL, $this->_server . "/distone/rest/service/price/fetch?" . $query);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: ' . $this->_getAccessToken($company)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = json_decode(curl_exec($ch));

        curl_close($ch);

        if (isset($response->_errors)) {
            throw new ErpServiceException($response->_errors[0]->_errorMsg, $response->_errors[0]->_errorNum); // find out the structure of ERP-ONE's errors
        }

        return $response;
    }

    /**
     * Type can be: invoice, pick, pack, order
     * Record is the record number
     * Sequence defaults to 1
     * 
     * Returns the following array:
     * 
     * type: type of document
     * record: record number
     * seq: record sequence
     * encoding: MIME type
     * document: encoded document
     * 
     * @param string $company
     * @param string $type
     * @param string $record
     * @param string|null $seq
     */
    public function getPdf($company, $type, $record, $seq = 1, $ch = null) {

        $ch = curl_init();

        $queryData = array(
            'type' => $type,
            'record' => $record,
            'seq' => $seq
        );

        $query = http_build_query($queryData);

        curl_setopt($ch, CURLOPT_URL, $this->_server . "/distone/rest/service/form/fetch?" . $query);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: ' . $this->_getAccessToken($company)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $r = curl_exec($ch);

        $response = json_decode($r);

        curl_close($ch);

        if (isset($response->_errors)) {
            throw new ErpServiceException($response->_errors[0]->_errorMsg, $response->_errors[0]->_errorNum); // find out the structure of ERP-ONE's errors
        }

        return $response;
    }

}
