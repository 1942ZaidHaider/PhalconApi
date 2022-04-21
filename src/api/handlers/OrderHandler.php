<?php

use Phalcon\Mvc\Controller;
use MongoDB\BSON\ObjectId;

class OrderHandler extends Controller
{
    public function initialize()
    {
        $this->table = $this->mongo->orders;
    }
    public function index()
    {
        return "order handler";
    }
    public function get()
    {
        $data = $this->table->find();
        $arr = [];
        foreach ($data as $r) {
            $arr[] = $r;
        }
        return json_encode($arr);
    }
    public function create()
    {
        if ($this->request->isPost() && ($this->request->getPost('product_id') && $this->request->getPost('qty'))) {
            $post = $this->request->getPost();
            $post['status']='paid';
            $response=$this->table->insertOne($post);
            $this->response->setStatusCode("200","Success");            
            $this->response->setContent("success");
            return $this->response->send();
        } else {
            $this->response->setStatusCode("400","Missing Data");
            $this->response->setContent('Missing Parameters');
            return $this->response->send();
        }
    }
    public function update()
    {
        if ($this->request->isPut() && ($this->request->getPut('order_id') && $this->request->getPut('status'))) {
            $put = $this->request->getPut();
            $response=$this->table->updateOne(["_id"=>new ObjectId($put['order_id'])],['$set'=>['status'=>$put['status']]]);
            $this->response->setStatusCode("200","Success");
            $this->response->setContent("Success");
            return $this->response->send();
        } else {
            $this->response->setStatusCode("400","Missing Data");
            $this->response->setContent('Missing Parameters');
            return $this->response->send();
        }
    }
}
