<?php
header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
// header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, GETm, OPTIONS, PATCH");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers:responseType, Content-Type, Access-Control-Allow-Headers, Authorization, X-REquested-With, X-Auth-User");
// ini_set('display_errors', '0');
date_default_timezone_set("Asia/Manila");
set_time_limit(1000);

define("SERVER", 'localhost');
define("DBASE", 'inventory-db');
define("USER", 'root');
define("PASSWORD", '');
define("CHARSET", 'utf8mb4');
define('SECRET', 'kamoteka');

class Connection
{
    protected $conString = "mysql:host=" . SERVER . ";dbname=" . DBASE . "; charset=" . CHARSET;
    protected $options = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false
    ];

    public function connect()
    {
        return new \PDO($this->conString, USER, PASSWORD, $this->options);
    }
}
