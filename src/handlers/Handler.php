<?php

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;
use MongoDB\BSON\Regex;

class Handler extends Controller
{
    public function initialize()
    {
        $this->table = $this->mongo->products;
    }
    public function index()
    {
        echo "<a href='https://github.com/1942ZaidHaider/PhalconApi#readme'>GITHUB</a><hr>";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, "https://raw.githubusercontent.com/1942ZaidHaider/PhalconApi/master/README.md");
        $data = curl_exec($curl);
        // $data= str_replace("    ","  ",$data);
        $data = str_replace("&ensp;", "&nbsp;", $data);
        $data = str_replace("\n/", "<hr>/", $data);
        $data = str_replace("\n", "<br>", $data);
        $data = str_replace("<br>**", "<br><b>", $data);
        $data = str_replace("**<br>", "</b><br>", $data);
        return $data;
    }
    public function insert()
    {
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['name']) && isset($post['price'])) {
                $result = $this->table->insertOne($post);
                $this->response->setStatusCode(200, "OK");
                return "Success";
            }
        }
        $this->response->setStatusCode(400, 'Missing data');
        return "missing data";
    }
    public function search($search = null)
    {
        $search = $search ? urldecode($search) : "";
        $terms = explode(" ", $search);
        foreach ($terms as $k => $v) {
            $terms[$k] = ['name' => new Regex('.*' . $v . '.*', "i")];
        }
        $find = ['$or' => $terms];
        //return json_encode($find);
        $res = $this->table->find($find);
        $arr = [];
        foreach ($res as $k => $v) {
            $arr[$k] = $v;
        }
        return json_encode($arr);
    }
    public function get()
    {
        $page = $this->request->getQuery('page') ?? 1;
        $perPage = $this->request->getQuery('per_page') ?? 5;
        $page = $page == "" ? 1 : $page;
        $perPage = $perPage == "" ? 5 : $perPage;
        $res = $this->table->find([], ["limit" => intval($perPage), "skip" => ($page - 1) * $perPage]);
        $arr = [];
        foreach ($res as $k => $v) {
            $arr[$k] = $v;
        }
        return json_encode($arr);
    }
    public function auth()
    {
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $this->response->setStatusCode(200, "OK");
            $key = "raxacoricofallapatorian";
            $currentTime = time();
            $expiry = $currentTime + (24 * 3600);
            $payload = [
                "iss" => '/',
                "aud" => '/',
                "iat" => $currentTime,
                "exp" => $expiry,
                "seed" => rand(99, 999),
            ];
            $token=JWT::encode($payload, $key, 'HS256');
            return $this->response->redirect($post['callback']."?access_token=".$token);
        } else {
            $this->response->setStatusCode(400, 'Missing data');
            return "missing data";
        }
    }
    public function register()
    {
        $callbackUrl = $this->request->getQuery("callback");
        if (!$callbackUrl) {
            $this->response->setStatusCode(400, "Missing Data");
            $this->response->setContent("Missing callback url");
            $this->response->send();
            die;
        }
        $data = [
            "callback" => $callbackUrl,
        ];
        return $this->view->render('register', $data);
    }
}
