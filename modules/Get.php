<?php
class Get
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function testGet(): array
    {
        return ["status" => "get ok"];
    }
}
