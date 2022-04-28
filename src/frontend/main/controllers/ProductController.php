<?php

use Phalcon\Mvc\Controller;
use MongoDB\BSON\ObjectId;

/**
 * Product Controller
 */
class ProductController extends Controller
{
    public function initialize()
    {
        $this->table = $this->mongo->products;
    }
    /**
     * User Login/Signup
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->products = $this->table->find()->toArray();
    }

    /**
     * WEBHOOK UPDATE
     *
     * @return void
     */
    public function updateAction()
    {
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $data = json_decode($post['data'], 1);
            $data["_id"] = new ObjectId($data["_id"]['$oid']);
            $response = $this->table->updateOne(["_id" => $data["_id"]], ['$set' => $data]);
        }
        return json_encode($response);
    }
    /**
     * WEBHOOK ADD
     *
     * @return void
     */
    public function addAction()
    {
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $data = json_decode($post['data'], 1);
            $data["_id"] = new ObjectId($data["_id"]['$oid']);
            $response = $this->table->insertOne($data);
        }
        return json_encode($response);
    }

    /**
     * Import products via api
     *
     * @return void
     */
    public function importAction()
    {
        $token = $this->session->token;
        $ip = $this->config->ip; //server ip address
        //
        // curl to get products;
        //
        $url = "http://$ip/api/products/get?access_token=$token&page=1&per_page=1000";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = json_decode(curl_exec($curl), 1);
        curl_close($curl);
        //
        //Fixing _id of the received data 
        //
        foreach ($data as $k => $x) {
            $data[$k]['_id'] = new ObjectId($x['_id']['$oid']);
        }
        $this->table->insertMany($data);
        $this->response->redirect('/frontend/');
    }

    /**
     * Place order
     *
     * @param string $id
     * @return void
     */
    public function buyAction(string $id)
    {
        $token = $this->session->token;
        $ip = $this->config->ip; //server ip address
        //
        // curl to get orders;
        //
        $url = "http://$ip/api/orders/create?access_token=$token&page=1&per_page=100";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        $body = [
            "product_id" => $id,
            "qty" => 1,
            "email" => $this->session->email
        ];
        print_r($_SESSION);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $data = curl_exec($curl);
        curl_close($curl);
        $this->response->redirect('/frontend/product');
    }
}
