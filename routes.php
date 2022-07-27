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
                'L2FkbWluLXJlZ2lzdGVy' => json_encode($this->auth->admin_register($data)),
                'L2FkbWluLWxvZ2lu' => json_encode($this->auth->admin_login($data)),
                'L2FkZC1wcm9kdWN0' => json_encode($this->post->AuthMiddleware()->addProduct($data)),
                'L2FkZC1wdXJjaGFzZXM=' => json_encode($this->post->AuthMiddleware()->addPurchases($data)),
                'L2FkZC10cmFuc2FjdGlvbg==' => json_encode($this->post->AuthMiddleware()->addTransaction($data)),
                'L2FkZC1zdXBwbGllcg==' => json_encode($this->post->AuthMiddleware()->addSupplier($data)),
                'L3JlZnJlc2h0b2tlbg==' => json_encode($this->auth->refreshToken()),
                'L2xvZ291dA==' => json_encode($this->auth->admin_logout()),
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
                'L2dldC1jYXRlZ29yaWVz' => json_encode($this->get->AuthMiddleware()->get_Categories()),
                'L2dldC1wcm9kdWN0cw==' => json_encode($this->get->AuthMiddleware()->get_Products()),
                'L2dldC1wcm9kdWN0cy1sYXJnZQ==' => json_encode($this->get->AuthMiddleware()->get_Products_Large()),
                'L2dldC1wdXJjaGFzZXM=' => json_encode($this->get->AuthMiddleware()->get_Purchases()),
                'L2dldC10cmFuc2FjdGlvbnM=' => json_encode($this->get->AuthMiddleware()->get_Transactions()),
                'L2dldC1zdXBwbGllcnM=' => json_encode($this->get->AuthMiddleware()->get_Suppliers()),
                'L2dldC1vcmRlcnMtdG9kYXk=' => json_encode($this->get->AuthMiddleware()->get_Orders_Today()),
                'L2dldC1jdXN0b21lcnMtdG9kYXk=' => json_encode($this->get->AuthMiddleware()->get_Transactions_Today()),
                'L2dldC10b3BzZWxsaW5nLXRvZGF5' => json_encode($this->get->AuthMiddleware()->get_Top_Selling_Products_Today()),
                'L2dldC10b3BzZWxsaW5nY2F0LXRvZGF5' => json_encode($this->get->AuthMiddleware()->get_Sales_By_Categories_Today()),
                'L2dldC10b3BzZWxsaW5nLW1vbnRo' => json_encode($this->get->AuthMiddleware()->get_Top_Selling_Products_Month()),
                'L2dldC10b3BzZWxsaW5nY2F0LW1vbnRo' => json_encode($this->get->AuthMiddleware()->get_Sales_By_Categories_Month()),
                'L2dldC1zYWxlcy1tb250aA==' => json_encode($this->get->AuthMiddleware()->get_Transactions_Month()),
                'L2dldC1zYWxlcy15ZWFy' => json_encode($this->get->AuthMiddleware()->get_Transactions_Current_Last_Year()),
                'L2dldC1leHBlbnNlcy15ZWFy' => json_encode($this->get->AuthMiddleware()->get_Purchases_Current_Last_Year()),
                'L2dldC1jdXN0b21lcnMteWVhcg==' => json_encode($this->get->AuthMiddleware()->get_Customers_Current_Last_Year()),
                'L2dldC1vcmRlcnMtY3VyeWVhcg==' => json_encode($this->get->AuthMiddleware()->get_Orders_Current_Year()),
                'L2dldC1wdXJjaGFzZXMtY3VyeWVhcg==' => json_encode($this->get->AuthMiddleware()->get_Purchases_Current_Year()),
                'L2dldC10cmFuc2FjdGlvbnMtY3VyeWVhcg==' => json_encode($this->get->AuthMiddleware()->get_Transactions_Current_Year()),
                'L2dldC1kZXRhaWxzLWN1cnllYXI=' => json_encode($this->get->AuthMiddleware()->get_detail_Current_Year()),
                'L2dldC1jdXN0b21lci1jdXJ5ZWFy' => json_encode($this->get->AuthMiddleware()->get_Customers_Current_Year()),
                'L2dldC1leHBlbnNlcy1jdXJ5ZWFy' => json_encode($this->get->AuthMiddleware()->get_Expenses_Current_Year()),
                'L2dldC1zdW1tYXJ5' => json_encode($this->get->AuthMiddleware()->summary()),
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
                'L3VwZGF0ZS1vcmRlcnM=' => json_encode($this->patch->AuthMiddleware()->updateTransaction($data)),
                'L3VwZGF0ZS1wcm9kdWN0' => json_encode($this->patch->AuthMiddleware()->updateProduct($data)),
                'L3VwZGF0ZS1zdXBwbGllcg==' => json_encode($this->patch->AuthMiddleware()->updateSupplier($data)),
                'L3VwZGF0ZS1wdXJjaGFzZQ==' => json_encode($this->patch->AuthMiddleware()->updatePurchase($data)),
                'L2RlbGV0ZS1wdXJjaGFzZQ==' => json_encode($this->patch->AuthMiddleware()->deletePurchase($data)),
                'L2RlbGV0ZS1wcm9kdWN0' => json_encode($this->patch->AuthMiddleware()->deleteProduct($data)),
                'L2RlbGV0ZS1zdXBwbGllcg==' => json_encode($this->patch->AuthMiddleware()->deleteSupplier($data)),
                'L2RlbGV0ZS1vcmRlcg==' => json_encode($this->patch->AuthMiddleware()->deleteTransaction($data)),
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
