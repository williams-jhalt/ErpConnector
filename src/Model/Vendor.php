<?php

namespace App\Model;

class Vendor {

    private $vendorNumber;
    private $name;
    private $email;

    public function getVendorNumber() {
        return $this->vendorNumber;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setVendorNumber($vendorNumber) {
        $this->vendorNumber = $vendorNumber;
        return $this;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

}
