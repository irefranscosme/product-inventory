<?php
namespace App\Model;

class Product {
  protected $properties = [
    "name" => null,
    "unit" => null,
    "price" => null,
    "date_expiry" => null,
    "image" => null,
  ];

  public function setColumn($column, $value) {
    $this->properties[$column] = $value;
  }

  public function getColumn($column) {
      return $this->properties[$column];
  }
}