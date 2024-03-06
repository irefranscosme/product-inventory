<?php
namespace App\Model;

class ProductInventory extends Product {

    private $inventoryProperties = [
        "quantity" => null,
        "cost" => null,
    ];

    function __construct()
    {
        $this->properties = [...$this->properties, ...$this->inventoryProperties];
    }
}