<?php

class AuthMiddleware
{
    public function AuthMiddleware(): static
    {
        if (isset(getallheaders()['Authorization'])) {
            if (getallheaders()['Authorization'] !== 'null' && $this->isAuthorized(getallheaders()['Authorization'])) {
                return $this;
            }
        }
        throw new Exception('Unauthorized');
    }

    public function isAuthorized($data): static
    {
        $tokenParts = explode('.', $data);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        $expiration = json_decode($payload)->exp;
        $is_token_expired = ($expiration - time()) < 0;

        $base64_url_header = $this->base64url_encode($header);
        $base64_url_payload = $this->base64url_encode($payload);

        $signature = hash_hmac('SHA256', "$base64_url_header.$base64_url_payload", SECRET, true);
        $base64_url_signature = $this->base64url_encode($signature);

        $is_signature_valid = ($base64_url_signature == $signature_provided);

        if ($is_token_expired || !$is_signature_valid) {
            throw new Exception('Unauthorized');
        }

        return $this;
    }

    public function base64url_encode($str): string
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }
}
