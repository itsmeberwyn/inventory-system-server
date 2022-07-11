<?php

require_once('./config/Config.php');
require_once('./modules/Response.php');
require_once('./modules/Get.php');
require_once('./modules/Post.php');
require_once('./modules/Auth.php');


class Route
{
    protected Connection $db;
    protected Get $get;
    protected Post $post;
    protected Auth $auth;
    protected PDO $pdo;

    public function __construct()
    {
        $this->db = new Connection();
        $this->pdo = $this->db->connect();

        $this->auth = new Auth($this->pdo);
        $this->get = new Get($this->pdo);
        $this->post = new Post($this->pdo);
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
                'add-transaction' => json_encode($this->post->addTransaction($data)),
                default => errorMessage(403)
            };
        } catch (Exception $e) {
            echo errorMessage(401);
            // echo $e;
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
}

$route = new Route();
$route->ClientRequest();
