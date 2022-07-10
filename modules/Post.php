<?php
class Post
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function testPost($data): array
    {
        return ["status" => "post ok"];
    }
}
