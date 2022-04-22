<?php

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;
use MongoDB\BSON\Regex;
use MongoDB\BSON\ObjectId;

class ProductHandler extends Controller
{
    public function initialize()
    {
        $this->table = $this->mongo->products;
    }
    public function insert()
    {
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            if (isset($post['name']) && isset($post['price'])) {
                $response = $this->table->insertOne($post);
                if ($response->getInsertedCount()) {
                    $this->response->setStatusCode("200", "Success");
                    $this->response->setContent("Success");
                    return $this->response->send();
                } else {
                    $this->response->setStatusCode("500", "Internal server error");
                    $this->response->setContent("Database malfunction");
                    return $this->response->send();
                }
            }
        } else {
            $this->response->setStatusCode(400, 'Bad Request');
            return "missing data";
        }
    }
    public function search($search = null)
    {
        $search = $search ? urldecode($search) : "";
        $terms = explode(" ", $search);
        foreach ($terms as $k => $v) {
            $terms[$k] = ['name' => new Regex('.*' . $v . '.*', "i")];
        }
        $find = ['$or' => $terms];
        $res = $this->table->find($find);
        $arr = $res->toArray();
        return json_encode($arr);
    }
    public function get($id)
    {
        $query = [];
        if ($id) {
            $query["_id"] = new ObjectId($id);
        }
        $page = $this->request->getQuery('page') ?? 1;
        $perPage = $this->request->getQuery('per_page') ?? 5;
        $page = $page == "" ? 1 : $page;
        $perPage = $perPage == "" ? 5 : $perPage;
        $response = $this->table->find($query, ["limit" => intval($perPage), "skip" => ($page - 1) * $perPage]);
        $arr = $response->toArray();
        if (count($arr)) {
            $this->response->setStatusCode("200", "Success");
            $this->response->setContent(json_encode($arr));
            return $this->response->send();
        } else {
            $this->response->setStatusCode("404", "Not Found");
            $this->response->setContent("Data Exhausted");
            return $this->response->send();
        }
    }
}
