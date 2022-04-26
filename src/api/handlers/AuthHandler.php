<?php

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;

/**
 * Authorization handler
 */
class AuthHandler extends Controller
{
    /**
     * Help block
     *
     * @return void
     */
    public function index()
    {
        echo "<a href='https://github.com/1942ZaidHaider/PhalconApi/tree/part2#readme'>GITHUB</a><hr>";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, "https://raw.githubusercontent.com/1942ZaidHaider/PhalconApi/part2/README.md");
        $data = curl_exec($curl);
        // $data= str_replace("    ","  ",$data);
        $data = str_replace("&ensp;", "&nbsp;", $data);
        $data = str_replace("\n/", "<hr>/", $data);
        $data = str_replace("\n", "<br>", $data);
        $data = str_replace("<br>**", "<br><b>", $data);
        $data = str_replace("**<br>", "</b><br>", $data);
        return $data;
    }
    /**
     * Generate access token
     *
     * @return void
     */
    public function auth()
    {
        if ($this->request->isPost()) {
            $post = $this->escaper->escapeArray($this->request->getPost());
            $this->response->setStatusCode(200, "OK");
            $key = "raxacoricofallapatorian";
            $currentTime = time();
            $expiry = $currentTime + (24 * 3600);
            $payload = [
                "iss" => '/',
                "aud" => '/',
                "iat" => $currentTime,
                "exp" => $expiry,
                "email" => $post['email'],
            ];
            $token = JWT::encode($payload, $key, 'HS256');
            return $this->response->redirect($post['callback'] . "?access_token=" . $token);
        } else {
            $this->response->setStatusCode(400, 'Missing data');
            return "missing data";
        }
    }
    /**
     * Generate view for requesting user email
     *
     * @return void
     */
    public function register()
    {
        $callbackUrl = $this->request->getQuery("callback");
        if (!$callbackUrl) {
            $this->response->setStatusCode(400, "Missing Data");
            $this->response->setContent("Missing callback url");
            return $this->response->send();
        } else {
            $callbackUrl=$this->escaper->escape($callbackUrl);
            $data = [
                "callback" => $callbackUrl,
            ];
            return $this->view->render('register', $data);
        }
    }
}
