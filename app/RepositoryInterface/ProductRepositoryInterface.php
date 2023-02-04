<?php

namespace App\RepositoryInterface;

interface ProductRepositoryInterface
{
    public function showAllProducts($products);

    public function showOneProduct($product);

    public function storeProduct($request);

    public function updateProduct($request, $product);

    public function deleteProduct($product);

    public function getAllScienteficNamesInSelect();

    public function CommercialNames();
}
