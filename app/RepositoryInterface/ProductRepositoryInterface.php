<?php

namespace App\RepositoryInterface;

interface ProductRepositoryInterface
{
    public function showAllProducts();

    public function showOneProductWithDetails($product);

    public function storeProduct($request);

    public function updateProduct($request, $product);

    public function deleteProduct($product);
}
