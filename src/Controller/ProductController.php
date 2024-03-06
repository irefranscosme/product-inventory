<?php

namespace App\Controller;

use App\Services\ProductService;
use App\Providers\View;
use App\Helpers\URL;
use App\Model\ProductInventory;
use Exception;

class ProductController
{
    public function index()
    {
        $url = new URL();

        $layout = new View();

        $layout->assign('link', $url->fullUrl());
        $layout->assign('content', $layout->get("Welcome"));
        $layout->render("Layout");
    }

    public function show($product_id = "")
    {
        $productService = new ProductService();
        $productService->fetch($product_id);
    }

    public function store()
    {

        $productInventory = new ProductInventory();
        $productInventory->setColumn("name", filter_var($_POST["name"]));
        $productInventory->setColumn("unit", filter_var($_POST["unit"]));
        $productInventory->setColumn("price", filter_var($_POST["price"]));
        $productInventory->setColumn("date_expiry", filter_var($_POST["date_exp"]));
        $productInventory->setColumn("quantity", filter_var($_POST["quantity"]));
        $productInventory->setColumn("image", $_FILES["product-image"]);

        $productService = new ProductService();
        $productService->insert($productInventory);
    }

    public function update($id)
    {

        $productInventory = new ProductInventory();
        $productInventory->setColumn("name", filter_var($_POST["name"]));
        $productInventory->setColumn("unit", filter_var($_POST["unit"]));
        $productInventory->setColumn("price", filter_var($_POST["price"]));
        $productInventory->setColumn("date_expiry", filter_var($_POST["date_exp"]));
        $productInventory->setColumn("quantity", filter_var($_POST["quantity"]));
        $productInventory->setColumn("image", $_FILES["product-image"]);

        $productService = new ProductService();
        $productService->update($productInventory, $id);
    }

    public function delete($id)
    {
        $productService = new ProductService();
        $productService->delete($id);
    }
}
