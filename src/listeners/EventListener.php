<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class EventListener
{
    public function beforeExecuteRoute($app, $isAuth, $key)
    {
        if (!$isAuth) {
            $token = $app->request->getQuery('access_token');
            if (!$token || $token == "") {
                $app->response->setStatusCode(403, "Forbidden");
                die("Forbidden");
            } else {
                try {
                    JWT::decode($token, new Key($key, 'HS256'));
                } catch( ExpiredException $e){
                    $app->response->setStatusCode(403, "Token Expired");
                    $app->response->setContent("Token Expired");                    
                    $app->response->send();                    
                    die;
                }
            }
        }
    }
}
