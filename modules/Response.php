<?php

function errorMessage($errCode)
{
    switch ($errCode) {
        case 400:
            $smg = "Bad request. Please contact the systems administrator.";
            break;
        case 401:
            $smg = "Unauthorized. Please contact the system administrator.";
            break;
        case 403:
            $smg = "Forbidden. Please contact the systems administrator.";
            break;
        default:
            $smg = "Request Not Found.";
            break;
    }

    http_response_code($errCode);
    return json_encode(['status' => ['remarks' => 'failed', "message" => $smg], 'timestamp' => date_create()]);
}

function response($payload, $remarks, $message, $code)
{
    $status = ['remarks' => $remarks, "message" => $message];
    http_response_code($code);
    return ['status' => $status, 'payload' => $payload, 'timestamp' => date_create()];
}
