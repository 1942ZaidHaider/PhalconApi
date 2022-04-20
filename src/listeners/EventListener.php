<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class EventListener
{
    public function beforeExecuteRoute($app, $isAuth, $key)
    {
        if (!$isAuth) {
            $token = $app->request->getQuery('access_token');
            //die($token);
            if (!$token || $token == "") {
                $app->response->setStatusCode(403, "Forbidden");
                die("Forbidden");
            } else {
                try {
                    $decoded = JWT::decode($token, new Key($key, 'HS256'));
                } catch (Exception $e) {
                    echo $e->getMessage();
                    die;
                }
            }
        }
    }
}
