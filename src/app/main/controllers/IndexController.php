<?php

use Phalcon\Mvc\Controller;
use MongoDB\BSON\ObjectId;

/**
 * Index Controller
 */
class IndexController extends Controller
{
    /**
     * User Login/Signup
     *
     * @return void
     */
    public function indexAction()
    {
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            switch ($post['action']) {
                case "login":
                    $resp = $this->mongo->users->findOne(['email' => $post['email'], 'password' => $post['password']]);
                    if ($resp) {
                        return $this->response->redirect("/app/index/webhooks");
                    } else {
                        $this->view->message = "<p class='alert alert-danger'>Invalid credentials or email not registered</p>";
                    }
                    break;
                case 'signup':
                    unset($post['action']);
                    $this->mongo->users->insertOne($post);
                    break;
            }
        }
    }
    /**
     * Admin Login/Signup
     *
     * @return void
     */
    public function adminAction()
    {
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            switch ($post['action']) {
                case "login":
                    $resp = $this->mongo->admins->findOne(['email' => $post['email'], 'password' => $post['password']]);
                    if ($resp) {
                        $url = urlencode("/app/index/callback");
                        return $this->response->redirect("/api/register?callback=$url");
                    } else {
                        $this->view->message = "<p class='alert alert-danger'>Invalid credentials or email not registered</p>";
                    }
                    break;
                case 'signup':
                    unset($post['action']);
                    $this->mongo->admins->insertOne($post);
                    break;
            }
        }
    }
    /**
     * List all orders
     *
     * @return void
     */
    public function listOrdersAction()
    {
        $token = $this->session->token;
        $ip = '192.168.2.6'; //server ip address
        //
        // curl to get orders;
        //
        $url = "http://$ip:8080/api/orders/get?access_token=$token";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        $this->view->orders = json_decode($data, 1);
    }
    /**
     * List all products
     *
     * @return void
     */
    public function listProductsAction()
    {
        $token = $this->session->token;
        $ip = '192.168.2.6'; //server ip address
        //
        // curl to get orders;
        //
        $url = "http://$ip:8080/api/products/get?access_token=$token&per_page=100";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        $this->view->products = json_decode($data, 1);
    }
    public function newProductAction($id = null)
    {
        $token = $this->session->token;
        $ip = '192.168.2.6'; //server ip address
        if ($id) {
            $url = "http://$ip:8080/api/products/get/$id?access_token=$token";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($curl);
            curl_close($curl);
            if ($this->request->isPost()) {
                $post=$this->request->getPost();
                $url = "http://$ip:8080/api/products/update/$id?access_token=$token";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POST, 1);
                unset($post["_id"]);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
                $data = curl_exec($curl);
                curl_close($curl);
                $this->response->redirect('/app/index/listproducts');
            }
            $this->view->product = json_decode($data, 1)[0];
        } else {
            if ($this->request->isPost()) {
                $post=$this->request->getPost();
                $url = "http://$ip:8080/api/products/insert?access_token=$token";
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POST, 1);
                unset($post["_id"]);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
                $data = curl_exec($curl);
                curl_close($curl);
                $this->response->redirect('/app/index/listproducts');

            }
            $this->view->product = [];
        }
        
    }

    // UTILITY CONTROLLERS

    /**
     * Callback url for api token generation
     *
     * @return void
     */
    public function callbackAction()
    {
        $this->session->token = $this->request->getQuery('access_token');
        $this->response->redirect("/app/index/listorders");
    }
    /**
     * Add new Webhooks
     *
     * @return void
     */
    public function webhooksAction()
    {
        if ($this->request->isPost()) {
            $post = $this->request->getPost();
            $temp = explode(":", $post['hook']);
            $db = $temp[0];
            $arr = [];
            $arr["type"] = $temp[1];
            $arr["name"] = $post['name'];
            $arr["url"] = $post['url'];
            $arr["key"] = $post['key'] ?? null;
            $this->webhookStore->$db->insertOne($arr);
        }
        $this->view->hooks = [
            "product:add" => "Products : add",
            "product:update" => "Products : update",
            "order:add" => "Orders : create",
            "order:update" => "Orders : update",
        ];
    }
}
