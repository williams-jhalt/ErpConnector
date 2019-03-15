<?php

namespace App\Repository;

use App\Model\Product;
use App\Service\ErpOneConnector;
use DateTime;

/**
 * Service to retrieve products from the database
 */
class ProductRepository {

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

        $query = "FOR EACH item NO-LOCK "
                . "WHERE item.company_it = '" . $company . "', "
                . "EACH wa_item NO-LOCK WHERE "
                . "wa_item.company_it = item.company_it AND "
                . "wa_item.item = item.item";

        $fields = "item.item,"
                . "item.descr,"
                . "wa_item.ship_location,"
                . "wa_item.list_price,"
                . "wa_item.qty_cmtd,"
                . "wa_item.qty_oh,"
                . "wa_item.warehouse,"
                . "item.um_display,"
                . "item.original_release_date,"
                . "item.manufacturer,"
                . "item.product_line,"
                . "item.date_added,"
                . "item.web_item,"
                . "item.is_deleted,"
                . "item.upc1";

        $items = $this->service->read($company, $query, $fields, $limit, $offset);

        $results = [];

        foreach ($items as $item) {
            $results[] = $this->_buildFromErp($item);
        }

        return $results;
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


        $query = "FOR EACH item NO-LOCK "
                . "WHERE item.company_it = '" . $company . "' "
                . "AND item.sy_lookup MATCHES '*{$searchTerms}*', "
                . "EACH wa_item NO-LOCK WHERE "
                . "wa_item.company_it = item.company_it AND "
                . "wa_item.item = item.item";

        $fields = "item.item,"
                . "item.descr,"
                . "wa_item.ship_location,"
                . "wa_item.list_price,"
                . "wa_item.qty_cmtd,"
                . "wa_item.qty_oh,"
                . "wa_item.warehouse,"
                . "item.um_display,"
                . "item.original_release_date,"
                . "item.manufacturer,"
                . "item.product_line,"
                . "item.date_added,"
                . "item.web_item,"
                . "item.is_deleted,"
                . "item.upc1";

        $items = $this->service->read($company, $query, $fields, $limit, $offset);

        $results = [];

        foreach ($items as $item) {
            $results[] = $this->_buildFromErp($item);
        }

        return $results;
    }

    /**
     * Retrieves a single product from the database,
     * id can be either the item number or barcode
     * 
     * @param string $itemNumber
     * @return Product
     */
    public function getItem(string $company, string $itemNumber) {

        $query = "FOR EACH item NO-LOCK "
                . "WHERE item.company_it = '" . $company . "' "
                . "AND item.item = '" . $itemNumber . "',"
                . "EACH wa_item NO-LOCK WHERE "
                . "wa_item.company_it = item.company_it AND "
                . "wa_item.item = item.item";

        $fields = "item.item,"
                . "item.descr,"
                . "wa_item.ship_location,"
                . "wa_item.list_price,"
                . "wa_item.qty_cmtd,"
                . "wa_item.qty_oh,"
                . "wa_item.warehouse,"
                . "item.um_display,"
                . "item.original_release_date,"
                . "item.manufacturer,"
                . "item.product_line,"
                . "item.date_added,"
                . "item.web_item,"
                . "item.is_deleted,"
                . "item.upc1";

        $items = $this->service->read($company, $query, $fields);        
        
        return $this->_buildFromErp($items[0]);
        
    }
    
    private function _buildFromErp($erpItem) {        
        
        $releaseDate = new DateTime($erpItem->item_date_added);
        
        $t = new Product();
        $t->setItemNumber($erpItem->item_item);
        $t->setName(join(" ", $erpItem->item_descr));
        $t->setReleaseDate($releaseDate->format('c'));
        $t->setBarcode($erpItem->item_upc1);
        $t->setUnitOfMeasure($erpItem->item_um_display);
        $t->setManufacturerCode($erpItem->item_manufacturer);
        $t->setProductTypeCode($erpItem->item_product_line);
        $t->setWholesalePrice($erpItem->wa_item_list_price);
        $t->setQuantityOnHand($erpItem->wa_item_qty_oh);
        $t->setQuantityAvailable($erpItem->wa_item_qty_oh - $erpItem->wa_item_qty_cmtd);
        
        return $t;
        
    }

}
