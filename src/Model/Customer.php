<?php

namespace App\Model;

class Customer {

    private $customerNumber;
    private $name;
    private $attentionFirstName;
    private $attentionLastName;
    private $attentionSuffix;
    private $billToAddress1;
    private $billToAddress2;
    private $billToAddress3;
    private $billToCity;
    private $billToState;
    private $billToPostalCode;
    private $billToCountry;
    private $billToPhone;
    private $billToPhoneExt;
    private $billToFax;
    private $billToCellPhone;
    private $salespersonId;
    private $email;
    
    public function getCustomerNumber() {
        return $this->customerNumber;
    }

    public function getName() {
        return $this->name;
    }

    public function getAttentionFirstName() {
        return $this->attentionFirstName;
    }

    public function getAttentionLastName() {
        return $this->attentionLastName;
    }

    public function getAttentionSuffix() {
        return $this->attentionSuffix;
    }

    public function getBillToAddress1() {
        return $this->billToAddress1;
    }

    public function getBillToAddress2() {
        return $this->billToAddress2;
    }

    public function getBillToAddress3() {
        return $this->billToAddress3;
    }

    public function getBillToCity() {
        return $this->billToCity;
    }

    public function getBillToState() {
        return $this->billToState;
    }

    public function getBillToPostalCode() {
        return $this->billToPostalCode;
    }

    public function getBillToCountry() {
        return $this->billToCountry;
    }

    public function getBillToPhone() {
        return $this->billToPhone;
    }

    public function getBillToPhoneExt() {
        return $this->billToPhoneExt;
    }

    public function getBillToFax() {
        return $this->billToFax;
    }

    public function getBillToCellPhone() {
        return $this->billToCellPhone;
    }

    public function getSalespersonId() {
        return $this->salespersonId;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setCustomerNumber($customerNumber) {
        $this->customerNumber = $customerNumber;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setAttentionFirstName($attentionFirstName) {
        $this->attentionFirstName = $attentionFirstName;
        return $this;
    }

    public function setAttentionLastName($attentionLastName) {
        $this->attentionLastName = $attentionLastName;
        return $this;
    }

    public function setAttentionSuffix($attentionSuffix) {
        $this->attentionSuffix = $attentionSuffix;
        return $this;
    }

    public function setBillToAddress1($billToAddress1) {
        $this->billToAddress1 = $billToAddress1;
        return $this;
    }

    public function setBillToAddress2($billToAddress2) {
        $this->billToAddress2 = $billToAddress2;
        return $this;
    }

    public function setBillToAddress3($billToAddress3) {
        $this->billToAddress3 = $billToAddress3;
        return $this;
    }

    public function setBillToCity($billToCity) {
        $this->billToCity = $billToCity;
        return $this;
    }

    public function setBillToState($billToState) {
        $this->billToState = $billToState;
        return $this;
    }

    public function setBillToPostalCode($billToPostalCode) {
        $this->billToPostalCode = $billToPostalCode;
        return $this;
    }

    public function setBillToCountry($billToCountry) {
        $this->billToCountry = $billToCountry;
        return $this;
    }

    public function setBillToPhone($billToPhone) {
        $this->billToPhone = $billToPhone;
        return $this;
    }

    public function setBillToPhoneExt($billToPhoneExt) {
        $this->billToPhoneExt = $billToPhoneExt;
        return $this;
    }

    public function setBillToFax($billToFax) {
        $this->billToFax = $billToFax;
        return $this;
    }

    public function setBillToCellPhone($billToCellPhone) {
        $this->billToCellPhone = $billToCellPhone;
        return $this;
    }

    public function setSalespersonId($salespersonId) {
        $this->salespersonId = $salespersonId;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }



}
