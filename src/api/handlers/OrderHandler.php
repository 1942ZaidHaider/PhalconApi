<?php

use Phalcon\Mvc\Controller;
use MongoDB\BSON\ObjectId;

/**
 * Order Cru
 */
class OrderHandler extends Controller
{
    public function initialize()
    {
        $this->table = $this->mongo->orders;
    }
    /**
     * Get all orders
     *
     * @return void
     */
    public function get()
    {
        $data = $this->table->find();
        $arr = $data->toArray();
        return json_encode($arr);
    }
    /**
     * Create new order
     *
     * @return void
     */
    public function create()
    {
        if (
            $this->request->isPost() &&
            $this->request->getPost('product_id') &&
            $this->request->getPost('qty') &&
            $this->request->getPost('email') 
        ) {
            $post = $this->escaper->escapeArray($this->request->getPost());
            $post['status'] = 'paid';
            $post['qty'] = intval($post['qty']);
            $response = $this->table->insertOne($post);
            if ($response->getInsertedCount()) {
                $order=$this->table->findOne(["_id" => new ObjectId($response->getInsertedId())]);
                $this->events->fire("Updates:orderCreate",$this,$order);
                $this->response->setStatusCode("200", "Success");
                $this->response->setContent("Success");
                return $this->response->send();
            } else {
                $this->response->setStatusCode("500", "Internal server error");
                $this->response->setContent("Database malfunction");
                return $this->response->send();
            }
        } else {
            $this->response->setStatusCode("400", "Missing Data");
            $this->response->setContent('Missing Parameters');
            return $this->response->send();
        }
    }
    /**
     * Update Order
     *
     * @return void
     */
    public function update()
    {
        if ($this->request->isPut() && ($this->request->getPut('order_id') && $this->request->getPut('status'))) {
            $put =  $this->escaper->escapeArray($this->request->getPut());
            try {
                $response = $this->table->updateOne(["_id" => new ObjectId($put['order_id'])], ['$set' => ['status' => $put['status']]]);
                $order=$this->table->findOne(["_id" => new ObjectId($put['order_id'])]);
                $this->events->fire("Updates:orderUpdate",$this,$order);
            } catch (InvalidArgumentException $e) {
                $this->response->setStatusCode("400", "Bad Request");
                $this->response->setContent('Malformed order_id');
                return $this->response->send();
            }
            if ($response->getMatchedCount()) {
                $this->response->setStatusCode("200", "Success");
                $this->response->setContent("Success");
                return $this->response->send();
            } else {
                $this->response->setStatusCode("404", "Product not found");
                $this->response->setContent("Product not found");
                return $this->response->send();
            }
        } else {
            $this->response->setStatusCode("400", "Missing Data");
            $this->response->setContent('Missing Parameters');
            return $this->response->send();
        }
    }
}
