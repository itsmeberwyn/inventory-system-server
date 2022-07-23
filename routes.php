<?php

require_once('./config/Config.php');
require_once('./modules/Response.php');
require_once('./modules/Get.php');
require_once('./modules/Post.php');
require_once('./modules/Patch.php');
require_once('./modules/Auth.php');


class Route
{
    protected Connection $db;
    protected Get $get;
    protected Post $post;
    protected Auth $auth;
    protected Patch $patch;
    protected PDO $pdo;

    public function __construct()
    {
        $this->db = new Connection();
        $this->pdo = $this->db->connect();

        $this->auth = new Auth($this->pdo);
        $this->get = new Get($this->pdo);
        $this->post = new Post($this->pdo);
        $this->patch = new Patch($this->pdo);
    }

    public function ClientRequest(): void
    {
        if (isset($_REQUEST['request'])) {
            $req = explode('/', rtrim($_REQUEST['request'], '/'));
        } else {
            $req = ['error' => 'Invalid Request'];
        }

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $this->PostRequest($req);
                break;
            case 'GET':
                $this->GetRequest($req);
                break;
            case 'PATCH':
                $this->PatchRequest($req);
                break;
            case 'OPTIONS':
                header('HTTP/1.1 200 OK');
                break;
            default:
                echo errorMessage(403);
                break;
        }
    }

    public function PostRequest($req): void
    {
        $data = json_decode(base64_decode(file_get_contents('php://input')));

        try {
            echo match ($req[0]) {
                'admin-register' => json_encode($this->auth->admin_register($data)),
                'admin-login' => json_encode($this->auth->admin_login($data)),
                'add-product' => json_encode($this->post->AuthMiddleware()->addProduct($data)),
                'add-purchases' => json_encode($this->post->AuthMiddleware()->addPurchases($data)),
                'add-transaction' => json_encode($this->post->AuthMiddleware()->addTransaction($data)),
                'add-supplier' => json_encode($this->post->AuthMiddleware()->addSupplier($data)),
                'refreshtoken' => json_encode($this->auth->refreshToken()),
                'logout' => json_encode($this->auth->admin_logout()),
                default => errorMessage(403)
            };
        } catch (Exception $e) {
            echo errorMessage(401);
            return;
        }
    }

    public function GetRequest($req): void
    {
        try {
            echo json_encode(["data" => base64_encode(json_encode(match ($req[0]) {
                'get-categories' => json_encode($this->get->AuthMiddleware()->get_Categories()),
                'get-products' => json_encode($this->get->AuthMiddleware()->get_Products()),
                'get-products-large' => json_encode($this->get->AuthMiddleware()->get_Products_Large()),
                'get-purchases' => json_encode($this->get->AuthMiddleware()->get_Purchases()),
                'get-transactions' => json_encode($this->get->AuthMiddleware()->get_Transactions()),
                'get-suppliers' => json_encode($this->get->AuthMiddleware()->get_Suppliers()),
                'get-orders-today' => json_encode($this->get->AuthMiddleware()->get_Orders_Today()),
                'get-customers-today' => json_encode($this->get->AuthMiddleware()->get_Transactions_Today()),
                'get-topselling-today' => json_encode($this->get->AuthMiddleware()->get_Top_Selling_Products_Today()),
                'get-topsellingcat-today' => json_encode($this->get->AuthMiddleware()->get_Sales_By_Categories_Today()),
                'get-topselling-month' => json_encode($this->get->AuthMiddleware()->get_Top_Selling_Products_Month()),
                'get-topsellingcat-month' => json_encode($this->get->AuthMiddleware()->get_Sales_By_Categories_Month()),
                'get-sales-month' => json_encode($this->get->AuthMiddleware()->get_Transactions_Month()),
                'get-sales-year' => json_encode($this->get->AuthMiddleware()->get_Transactions_Current_Last_Year()),
                'get-expenses-year' => json_encode($this->get->AuthMiddleware()->get_Purchases_Current_Last_Year()),
                'get-customers-year' => json_encode($this->get->AuthMiddleware()->get_Customers_Current_Last_Year()),
                'get-orders-curyear' => json_encode($this->get->AuthMiddleware()->get_Orders_Current_Year()),
                'get-purchases-curyear' => json_encode($this->get->AuthMiddleware()->get_Purchases_Current_Year()),
                'get-transactions-curyear' => json_encode($this->get->AuthMiddleware()->get_Transactions_Current_Year()),
                'get-details-curyear' => json_encode($this->get->AuthMiddleware()->get_detail_Current_Year()),
                'get-customer-curyear' => json_encode($this->get->AuthMiddleware()->get_Customers_Current_Year()),
                'get-expenses-curyear' => json_encode($this->get->AuthMiddleware()->get_Expenses_Current_Year()),
                'get-summary' => json_encode($this->get->AuthMiddleware()->summary()),
                default => errorMessage(403)
            }))]);
        } catch (Exception $e) {
            echo errorMessage(401);
            return;
        }
    }

    public function PatchRequest($req): void
    {
        $data = json_decode(base64_decode(file_get_contents('php://input')));

        try {
            echo match ($req[0]) {
                'update-orders' => json_encode($this->patch->AuthMiddleware()->updateTransaction($data)),
                'update-product' => json_encode($this->patch->AuthMiddleware()->updateProduct($data)),
                'update-supplier' => json_encode($this->patch->AuthMiddleware()->updateSupplier($data)),
                'update-purchase' => json_encode($this->patch->AuthMiddleware()->updatePurchase($data)),
                'delete-purchase' => json_encode($this->patch->AuthMiddleware()->deletePurchase($data)),
                'delete-product' => json_encode($this->patch->AuthMiddleware()->deleteProduct($data)),
                'delete-supplier' => json_encode($this->patch->AuthMiddleware()->deleteSupplier($data)),
                'delete-order' => json_encode($this->patch->AuthMiddleware()->deleteTransaction($data)),
                default => errorMessage(403)
            };
        } catch (Exception $e) {
            // echo errorMessage(401);
            echo $e;
            return;
        }
    }
}

$route = new Route();
$route->ClientRequest();
