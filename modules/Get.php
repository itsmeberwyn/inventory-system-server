<?php
class Get
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // inventory 
    public function get_Categories()
    {
    }

    public function get_Products()
    {
    }

    public function get_Purchases()
    {
    }

    public function get_Suppliers()
    {
    }

    // sales report
    public function get_Orders_Today()
    {
    }

    public function get_Transactions_Today()
    {
    }

    public function get_Top_Selling_Products_Today()
    {
    }

    public function get_Sales_By_Categories_Today()
    {
    }

    public function get_Top_Selling_Products_Month()
    {
    }

    public function get_Sales_By_Categories_Month()
    {
    }

    public function get_Transactions_Month()
    {
    }

    public function get_Transactions_Current_Last_Year()
    {
    }

    public function get_Purchases_Current_Last_Year()
    {
    }

    public function get_Customers_Current_Last_Year()
    {
    }

    public function get_Orders_Current_Year()
    {
    }

    public function get_Purchases_Current_Year()
    {
    }

    public function get_Transactions_Current_Year()
    {
    }
}
