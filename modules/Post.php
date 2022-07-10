<?php
class Post
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // inventory
    public function addProduct($product)
    {
    }

    public function addPurchase($product)
    {
    }

    public function addSupplier($supplier)
    {
    }

    // pos
    public function addOrder($order)
    {
    }

    public function addTransaction($order)
    {
    }
}
