<?php

namespace App\Model;

class Product {

    private $itemNumber;
    private $name;
    private $releaseDate;
    private $barcode;
    private $unitOfMeasure;
    private $manufacturerCode;
    private $productTypeCode;
    private $wholesalePrice;
    private $quantityOnHand;
    private $quantityAvailable;

    public function getItemNumber() {
        return $this->itemNumber;
    }

    public function getName() {
        return $this->name;
    }

    public function getReleaseDate() {
        return $this->releaseDate;
    }

    public function getBarcode() {
        return $this->barcode;
    }

    public function getUnitOfMeasure() {
        return $this->unitOfMeasure;
    }

    public function getManufacturerCode() {
        return $this->manufacturerCode;
    }

    public function getProductTypeCode() {
        return $this->productTypeCode;
    }

    public function getWholesalePrice() {
        return $this->wholesalePrice;
    }

    public function getQuantityOnHand() {
        return $this->quantityOnHand;
    }

    public function getQuantityAvailable() {
        return $this->quantityAvailable;
    }

    public function setItemNumber($itemNumber) {
        $this->itemNumber = $itemNumber;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setReleaseDate($releaseDate) {
        $this->releaseDate = $releaseDate;
        return $this;
    }

    public function setBarcode($barcode) {
        $this->barcode = $barcode;
        return $this;
    }

    public function setUnitOfMeasure($unitOfMeasure) {
        $this->unitOfMeasure = $unitOfMeasure;
        return $this;
    }

    public function setManufacturerCode($manufacturerCode) {
        $this->manufacturerCode = $manufacturerCode;
        return $this;
    }

    public function setProductTypeCode($productTypeCode) {
        $this->productTypeCode = $productTypeCode;
        return $this;
    }

    public function setWholesalePrice($wholesalePrice) {
        $this->wholesalePrice = $wholesalePrice;
        return $this;
    }

    public function setQuantityOnHand($quantityOnHand) {
        $this->quantityOnHand = $quantityOnHand;
        return $this;
    }

    public function setQuantityAvailable($quantityAvailable) {
        $this->quantityAvailable = $quantityAvailable;
        return $this;
    }

}
