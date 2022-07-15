
<?php

require_once('./middleware/Auth.Middleware.php');

class Get extends AuthMiddleware
{
    // NOTE: missing method!
    // sales report
    // first page->fetching revenue data 
    // second page->cards data 

    // NOTE: SUGGESTIONS!
    // sales report
    // possible to use params for dymanic change of data
    // possible to add data picker to sales report page

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
                $payload = array_chunk($res, 6);
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

    public function get_Products_Large()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory products';

        $sql = "SELECT * FROM products WHERE is_deleted IS NULL";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = array_chunk($res, 8);
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
    // sold items today
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

    // total customers today
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

    // top selling products today
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

    // sales by categories
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
    // top selling products month
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

    // top selling by categories / month
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
            AND transactions.is_deleted IS NULL 
        GROUP BY DAY(transactions.created_at);";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $dataMonth = array_fill(0, cal_days_in_month(CAL_GREGORIAN, intval(date('m')), intval(date('Y'))), 0);

                foreach ($res as $dailyData) {
                    $dataMonth[$dailyData['day'] - 1] = $dailyData['sales'];
                }

                $payload = [
                    $dataMonth
                ];
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

    // sales from last year to current year
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
            AND transactions.is_deleted IS NULL 
        GROUP BY MONTH(transactions.created_at);";

        $sql2 = "SELECT SUM(orders.subTotal) as sales, MONTH(transactions.created_at) AS month 
        FROM transactions, orders
        WHERE YEAR(transactions.created_at) = YEAR(CURDATE()) 
            AND orders.transactionId=transactions.id 
        GROUP BY MONTH(transactions.created_at);";

        try {
            if ($res1 = $this->pdo->query($sql1)->fetchAll()) {
                $dataLastYear = array_fill(0, 12, 0);
                $dataCurrentYear = array_fill(0, 12, 0);
                foreach ($res1 as $lastyeardata) {
                    $dataLastYear[$lastyeardata['month'] - 1] = $lastyeardata['sales'];
                }
                if ($res2 = $this->pdo->query($sql2)->fetchAll()) {
                    foreach ($res2 as $currentyeardata) {
                        $dataCurrentYear[$currentyeardata['month'] - 1] = $currentyeardata['sales'];
                    }
                }
                $payload = [
                    (date('Y') - 1) => $dataLastYear,
                    date('Y') => $dataCurrentYear,
                ];
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved sales";
            } else {
                $message = 'No records found for sales';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    // purchases from last year to current year
    public function get_Purchases_Current_Last_Year()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get expenses from the database';

        $sql1 = "SELECT SUM(purchases.quantityBought) as expenses, purchases.price, MONTH(purchases.created_at) AS month 
        FROM purchases
        WHERE YEAR(purchases.created_at) = YEAR(CURDATE())-1
        AND purchases.is_deleted IS NULL 
        GROUP BY MONTH(purchases.created_at);";

        $sql2 = "SELECT SUM(purchases.quantityBought) as expenses, purchases.price, MONTH(purchases.created_at) AS month 
        FROM purchases
        WHERE YEAR(purchases.created_at) = YEAR(CURDATE())
        AND purchases.is_deleted IS NULL 
        GROUP BY MONTH(purchases.created_at);";

        try {
            if ($res1 = $this->pdo->query($sql1)->fetchAll()) {
                $dataLastYear = array_fill(0, 12, 0);
                $dataCurrentYear = array_fill(0, 12, 0);
                foreach ($res1 as $lastyeardata) {
                    $dataLastYear[$lastyeardata['month'] - 1] = $lastyeardata['sales'] * $lastyeardata['price'];
                }
                if ($res2 = $this->pdo->query($sql2)->fetchAll()) {
                    foreach ($res2 as $currentyeardata) {
                        $dataCurrentYear[$currentyeardata['month'] - 1] = $currentyeardata['sales'] * $currentyeardata['price'];
                    }
                }
                $payload = [
                    (date('Y') - 1) => $dataLastYear,
                    date('Y') => $dataCurrentYear,
                ];
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved expenses";
            } else if ($res2 = $this->pdo->query($sql2)->fetchAll()) {
                $dataCurrentYear = array_fill(0, 12, 0);
                foreach ($res2 as $currentyeardata) {
                    $dataCurrentYear[$currentyeardata['month'] - 1] = $currentyeardata['expenses'] * $currentyeardata['price'];
                }
                $payload = [
                    (date('Y') - 1) => [],
                    date('Y') => $dataCurrentYear,
                ];
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved expenses";
            } else {
                $message = 'No records found for expenses';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    // total customer from last year to current year
    public function get_Customers_Current_Last_Year()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get expenses from the database';

        $sql1 = "SELECT count(transactions.id) AS totalCustomer, MONTH(transactions.created_at) AS month 
        FROM transactions
        WHERE YEAR(transactions.created_at) = YEAR(CURDATE())-1
        AND transactions.is_deleted IS NULL 
        GROUP BY MONTH(transactions.created_at);";

        $sql2 = "SELECT count(transactions.id) AS totalCustomer, MONTH(transactions.created_at) AS month 
        FROM transactions
        WHERE YEAR(transactions.created_at) = YEAR(CURDATE())
        AND transactions.is_deleted IS NULL 
        GROUP BY MONTH(transactions.created_at);";

        try {
            if ($res1 = $this->pdo->query($sql1)->fetchAll()) {
                $dataLastYear = array_fill(0, 12, 0);
                $dataCurrentYear = array_fill(0, 12, 0);

                foreach ($res1 as $lastyeardata) {
                    $dataLastYear[$lastyeardata['month'] - 1] = $lastyeardata['totalCustomer'];
                }

                if ($res2 = $this->pdo->query($sql2)->fetchAll()) {
                    foreach ($res2 as $currentyeardata) {
                        $dataCurrentYear[$currentyeardata['month'] - 1] = $currentyeardata['totalCustomer'];
                    }
                }
                $payload = [
                    (date('Y') - 1) => $dataLastYear,
                    date('Y') => $dataCurrentYear,
                ];
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved expenses";
            } else if ($res2 = $this->pdo->query($sql2)->fetchAll()) {
                $dataLastYear = array_fill(0, 12, 0);
                $dataCurrentYear = array_fill(0, 12, 0);
                foreach ($res2 as $currentyeardata) {
                    $dataCurrentYear[$currentyeardata['month'] - 1] = $currentyeardata['totalCustomer'];
                }
                $payload = [
                    (date('Y') - 1) => $dataLastYear,
                    date('Y') => $dataCurrentYear,
                ];
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved expenses";
            } else {
                $message = 'No records found for expenses';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    // 4th page
    // -> get sold items
    public function get_Orders_Current_Year()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory orders';

        $sql = "SELECT SUM(orders.quantity) as quantitySold, SUM(orders.subTotal) as revenue
        FROM transactions, orders 
        WHERE YEAR(transactions.created_at) = YEAR(CURRENT_DATE())
            AND orders.transactionId=transactions.id 
            AND transactions.is_deleted IS NULL";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved orders";
            } else {
                $message = 'No records found for orders';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    // total purchase cost, total item purchased
    public function get_Purchases_Current_Year()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get inventory purchases';

        $sql = "SELECT SUM(purchases.quantityBought) as quantityBought, SUM(purchases.quantityBought)*purchases.price as cost
        FROM purchases 
        WHERE YEAR(purchases.created_at) = YEAR(CURRENT_DATE())
            AND purchases.is_deleted IS NULL
            GROUP BY purchases.productId";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $quantity = 0;
                $cost = 0;
                foreach ($res as $row) {
                    $quantity += $row['quantityBought'];
                    $cost += $row['cost'];
                }

                $payload = [
                    "quantity" => $quantity,
                    "cost" => $cost
                ];
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

    // total customers count
    public function get_Transactions_Current_Year()
    {
        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get total customers from the database';

        $sql = "SELECT COUNT(transactions.id) as totalCustomers
        FROM transactions
        WHERE YEAR(transactions.created_at) = YEAR(CURDATE())
            AND transactions.is_deleted IS NULL";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                $payload = $res;
                $code = 200;
                $remarks = "success";
                $message = "Successfully retrieved total customers";
            } else {
                $message = 'No records found for customers information';
            }
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }

    // summary
    public function summary()
    {

        $summaryMonth = array_fill(0, 12, array_fill(0, 4, 0));

        $payload = [];
        $code = 404;
        $remarks = 'failed';
        $message = 'Failed to get the summary information from the database';

        $sql = "SELECT SUM(orders.quantity) as soldItems, SUM(orders.subTotal) AS sales, MONTH(transactions.created_at) AS month
        FROM transactions, orders
        WHERE YEAR(transactions.created_at) = YEAR(CURDATE())
            AND orders.transactionId=transactions.id 
            AND transactions.is_deleted IS NULL
            GROUP BY MONTH(transactions.created_at)";


        $sq1 = "SELECT COUNT(transactions.id) as totalCustomers, MONTH(transactions.created_at) AS month
        FROM transactions
        WHERE YEAR(transactions.created_at) = YEAR(CURDATE())
            AND transactions.is_deleted IS NULL
            GROUP BY MONTH(transactions.created_at)";

        $sql2 = "SELECT SUM(purchases.quantityBought)*purchases.price as cost, MONTH(purchases.created_at) AS month
        FROM purchases 
        WHERE YEAR(purchases.created_at) = YEAR(CURRENT_DATE())
            AND purchases.is_deleted IS NULL
            GROUP BY purchases.productId";

        try {
            if ($res = $this->pdo->query($sql)->fetchAll()) {
                foreach ($res as $row) {
                    $summaryMonth[$row['month'] - 1][0] = $row['soldItems'];
                    $summaryMonth[$row['month'] - 1][3] = $row['sales'];
                }
            }
            if ($res = $this->pdo->query($sq1)->fetchAll()) {
                foreach ($res as $row) {
                    $summaryMonth[$row['month'] - 1][1] = $row['totalCustomers'];
                }
            }
            if ($res = $this->pdo->query($sql2)->fetchAll()) {
                foreach ($res as $row) {
                    $summaryMonth[$row['month'] - 1][2] = $row['cost'];
                }
            }

            $payload = $summaryMonth;
            $code = 200;
            $remarks = "success";
            $message = "Successfully retrieved the summary information";
        } catch (\PDOException $e) {
            $message = $e->getMessage();
            $code = 403;
        }

        return response($payload, $remarks, $message, $code);
    }
}
