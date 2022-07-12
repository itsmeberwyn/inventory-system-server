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
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory category information';

        $sql = "SELECT * FROM categories";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved categories from inventory";
            } else {
                $message = 'No records found for category information';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    public function get_Products()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory products';

        $sql = "SELECT * FROM products WHERE is_deleted IS NULL";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved inventory products";
            } else {
                $message = 'No records found for category information';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    public function get_Purchases()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory purchases';

        $sql = "SELECT * FROM purchases WHERE is_deleted IS NULL";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved purchases";
            } else {
                $message = 'No records found for purchases';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    public function get_Suppliers()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get suppliers information';

        $sql = "SELECT * FROM suppliers WHERE is_deleted IS NULL";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved suppliers information";
            } else {
                $message = 'No records found for suppliers';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    // sales report
    // first page of sales report
    public function get_Orders_Today()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory report this day';

        $sql = "SELECT count(*) as soldItemsToday FROM orders 
        WHERE DATE(created_at) = CURDATE()";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved orders today";
            } else {
                $message = 'No records found for orders today';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    public function get_Transactions_Today()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory report this day';

        $sql = "SELECT count(*) as customersToday FROM transactions 
        WHERE DATE(created_at) = CURDATE() 
            AND is_deleted IS NULL";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved customers today";
            } else {
                $message = 'No records found for customers today';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    public function get_Top_Selling_Products_Today()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory top selling products for today';

        $sql = "SELECT SUM(orders.quantity) as quantitySold, SUM(orders.subTotal) as sold, transactions.id, transactions.is_deleted, products.productName, products.price 
        FROM transactions, orders, products 
        WHERE DATE(transactions.created_at) = CURDATE() 
            AND orders.transactionId=transactions.id 
            AND orders.productId=products.id 
            AND transactions.is_deleted IS NULL 
        GROUP BY orders.productId 
        ORDER BY quantitySold DESC";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved top selling products today";
            } else {
                $message = 'No records found for top selling products today';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    public function get_Sales_By_Categories_Today()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory top selling products by categories today';

        $sql = "SELECT SUM(orders.quantity) as quantitySold, SUM(orders.subTotal) as sold, transactions.is_deleted, categories.categoryName 
        FROM transactions, orders, products, categories 
        WHERE DATE(transactions.created_at) = CURDATE() 
            AND products.categoryId=categories.id 
            AND orders.transactionId=transactions.id 
            AND orders.productId=products.id 
            AND transactions.is_deleted IS NULL 
        GROUP BY products.categoryId 
        ORDER BY quantitySold DESC";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved top selling products by categories today";
            } else {
                $message = 'No records found for top selling products by categories today';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    // second page of sales report
    public function get_Top_Selling_Products_Month()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory top selling products for this month';

        $sql = "SELECT SUM(orders.quantity) as quantitySold, SUM(orders.subTotal) as sold, transactions.created_at, transactions.id, transactions.is_deleted, products.productName, products.price 
        FROM transactions, orders, products 
        WHERE MONTH(transactions.created_at) = MONTH(CURRENT_DATE()) 
            AND YEAR(transactions.created_at) = YEAR(CURRENT_DATE())
            AND orders.transactionId=transactions.id 
            AND orders.productId=products.id 
            AND transactions.is_deleted IS NULL 
        GROUP BY orders.productId 
        ORDER BY quantitySold DESC";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved top selling products for this month";
            } else {
                $message = 'No records found for top selling products for this month';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    public function get_Sales_By_Categories_Month()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory top selling products by categories for this month';

        $sql = "SELECT SUM(orders.quantity) as quantitySold, SUM(orders.subTotal) as sold, transactions.is_deleted, categories.categoryName 
        FROM transactions, orders, products, categories 
        WHERE MONTH(transactions.created_at) = MONTH(CURRENT_DATE())
            AND YEAR(transactions.created_at) = YEAR(CURRENT_DATE())
            AND products.categoryId=categories.id 
            AND orders.transactionId=transactions.id 
            AND orders.productId=products.id 
            AND transactions.is_deleted IS NULL 
        GROUP BY products.categoryId 
        ORDER BY quantitySold DESC";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved top selling products by categories for this month";
            } else {
                $message = 'No records found for top selling products by categories for this month';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    // sales daily for a month
    public function get_Transactions_Month()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory sales for this month';

        $sql = "SELECT SUM(orders.subTotal) as sales, DAY(transactions.created_at) AS day FROM transactions, orders
        WHERE MONTH(transactions.created_at) = MONTH(CURDATE()) 
            AND orders.transactionId=transactions.id 
        GROUP BY DAY(transactions.created_at);";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved sales for this month";
            } else {
                $message = 'No records found for sales for this month';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    public function get_Transactions_Current_Last_Year()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get sales from the database';

        $sql1 = "SELECT SUM(orders.subTotal) as sales, MONTH(transactions.created_at) AS month 
        FROM transactions, orders
        WHERE YEAR(transactions.created_at) = YEAR(CURDATE())-1
            AND orders.transactionId=transactions.id 
        GROUP BY MONTH(transactions.created_at);";

        $sql2 = "SELECT SUM(orders.subTotal) as sales, MONTH(transactions.created_at) AS month 
        FROM transactions, orders
        WHERE YEAR(transactions.created_at) = YEAR(CURDATE()) 
            AND orders.transactionId=transactions.id 
        GROUP BY MONTH(transactions.created_at);";

        try {
            if ($res1 = $this->pdo->query($sql1)->fetchAll()) {
                if ($res2 = $this->pdo->query($sql2)->fetchAll()) {

                    $payload = [
                        "2021" => $res1,
                        "2022" => $res2,
                    ];
                    $code = 200;
                    $remarks = "success";
                    $message = "Successfully retrieved sales";
                }
            } else {
                $message = 'No records found for sales';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
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
