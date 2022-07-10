<?php

class Patch
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // inventory
    // update
    public function updateProduct($product)
    {
    }

    public function updateSupplier($supplier)
    {
    }

    // delete
    public function deleteProduct($product)
    {
    }
    public function deleteSupplier($supplier)
    {
    }

    // pos
    // update
    public function updateOrder($order)
    {
    }

    // delete
    public function deleteOrder($order)
    {
    }
}
