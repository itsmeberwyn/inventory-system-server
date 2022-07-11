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
        $data = json_decode(file_get_contents('php://input'));

        try {
            echo match ($req[0]) {
                'admin-register' => json_encode($this->auth->admin_register($data)),
                'admin-login' => json_encode($this->auth->admin_login($data)),
                'add-product' => json_encode($this->post->addProduct($data)),
                'add-purchases' => json_encode($this->post->addPurchases($data)),
                'add-transaction' => json_encode($this->post->addTransaction($data)),
                'add-supplier' => json_encode($this->post->addSupplier($data)),
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
            echo match ($req[0]) {
                default => errorMessage(403)
            };
        } catch (Exception $e) {
            echo errorMessage(401);
            return;
        }
    }

    public function PatchRequest($req): void
    {
        $data = json_decode(file_get_contents('php://input'));

        try {
            echo match ($req[0]) {
                'update-orders' => json_encode($this->patch->updateTransaction($data)),
                'update-product' => json_encode($this->patch->updateProduct($data)),
                'update-supplier' => json_encode($this->patch->updateSupplier($data)),
                'delete-product' => json_encode($this->patch->deleteProduct($data)),
                'delete-supplier' => json_encode($this->patch->deleteSupplier($data)),
                'delete-order' => json_encode($this->patch->deleteTransaction($data)),
                default => errorMessage(403)
            };
        } catch (Exception $e) {
            echo errorMessage(401);
            return;
        }
    }
}

$route = new Route();
$route->ClientRequest();
