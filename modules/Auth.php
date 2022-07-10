<?php

require_once('./middleware/Auth.Middleware.php');

class Auth extends AuthMiddleware
{
    protected PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function admin_register(object $data): array
    {
        $payload = [];
        $code = 500;
        $remarks = 'failed';
        $message = 'There was an error registering the admin';

        $sql = "INSERT INTO users (username, password, role) VALUES (?,?,?)";

        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $data->username,
            password_hash($data->password, PASSWORD_DEFAULT),
            $data->role,
        ]);

        $count = $sql->rowCount();
        $last_id = $this->pdo->lastInsertId();

        if ($count) {
            $payload = [
                "id" => $last_id,
                "username" => $data->username,
            ];
            $code = 200;
            $remarks = "success";
            $message = "Successfully registered user";
        }

        return response($payload, $remarks, $message, $code);
    }

    public function admin_login($data): array
    {
        $payload = [];
        $code = 403;
        $remarks = 'failed';
        $message = 'The username or password is incorrect';

        $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $data->username
        ]);

        $res = $sql->fetch(PDO::FETCH_ASSOC);

        if ($res && password_verify($data->password, $res['password'])) {
            $refreshToken = $this->generate_jwt($res['username'], false);
            $jwt = $this->generate_jwt($res['username']);

            $cookie_options = array(
                'expires' => time() + 600,
                'path' => '/',
                'httpOnly' => true,
                'secure' => true,
                'samesite' => 'Strict',
            );

            setCookie('_uc_rt', $refreshToken, $cookie_options);

            $payload = [
                'user_id' => $res['id'],
                'username' => $res['username'],
                'role' => 'admin',
                'access_token' => $jwt,
            ];
            $code = 200;
            $remarks = 'success';
            $message = 'Successfully logged in';
        }
        return response($payload, $remarks, $message, $code);
    }

    protected function generateHeader(): string
    {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
            'app' => 'inventorymngmtsystem',
        ];

        return $this->base64url_encode(json_encode($header));
    }

    protected function generatePayload($username, $accesstoken): string
    {
        $payload = [
            'iss' => $username,
            'iby' => 'dev',
            'iat' => time(),
            'exp' => (time() + ($accesstoken ? 60 : 600))
        ];

        return $this->base64url_encode(json_encode($payload));
    }

    protected function generate_jwt($username, $accesstoken = true): string
    {
        $payload_encoded = $this->generatePayload($username, $accesstoken);
        $header_encoded = $this->generateHeader();

        $signature = hash_hmac('SHA256', "$header_encoded.$payload_encoded", SECRET, true);
        $signature_encoded = $this->base64url_encode($signature);

        return "$header_encoded.$payload_encoded.$signature_encoded";
    }

    public function refreshToken(): array
    {
        $payload = [];
        $code = 400;
        $remarks = 'failed';
        $message = "There was an error refreshing your token from the server";

        if (isset($_COOKIE['_uc_rt'])) {

            $refreshToken = $_COOKIE['_uc_rt'];

            try {
                if ($this->isAuthorized($refreshToken)) {
                    $tokenParts = explode('.', $refreshToken);
                    $jwt = $this->generate_jwt(json_decode(base64_decode($tokenParts[1]))->iss);

                    $payload = [
                        'accesstoken' => $jwt,
                    ];
                }
            } catch (Exception $e) {
                throw new Exception('Unauthorized');
            }

            $code = 200;
            $remarks = 'success';
            $message = 'Successfully renewed token';
        }
        return response($payload, $remarks, $message, $code);
    }

    public function admin_logout()
    {
        $payload = [];
        $code = 500;
        $remarks = 'failed';
        $message = 'There was an error on the server. Please try again later';

        if (isset($_COOKIE['_uc_rt'])) {
            unset($_COOKIE['_uc_rt']);
            setcookie('_uc_rt', null, -1, '/');

            $code = 200;
            $remarks = 'success';
            $message = 'Successfully logged out';
        }

        return response($payload, $remarks, $message, $code);
    }
}
