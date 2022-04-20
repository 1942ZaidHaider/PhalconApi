<?php

use Phalcon\Mvc\Controller;
use MongoDB\BSON\Regex;

class Handler extends Controller
{
    public function initialize()
    {
        $this->table = $this->mongo->products;
    }
    public function index()
    {
        echo "Welcome to the api";
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
    public function search($search)
    {
        $search = urldecode($search);
        $terms = explode(" ", $search);
        foreach ($terms as $k => $v) {
            $terms[$k] = ['name' => new Regex('.*' . $v . '.*',"i")];
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
            return $this->jwt;
        }
        $this->response->setStatusCode(400, 'Missing data');
        return "missing data";
    }
}
