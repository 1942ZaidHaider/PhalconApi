<?php

use Phalcon\Mvc\Controller;
/**
 * Index Controller
 */
class IndexController extends Controller
{
    /**
     * Admin Login/Signup
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
                        return $this->response->redirect("/index/list");
                    } else {
                        $this->view->message="<p class='alert alert-danger'>Invalid credentials or email not registered</p>";
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
     * List all orders
     *
     * @return void
     */
    public function listAction()
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
    public function tokenAction()
    {
        $url = urlencode("/index/callback");
        $this->response->redirect("/api/register?callback=$url");
    }
    public function callbackAction()
    {
        $this->session->token = $this->request->getQuery('access_token');
        $this->response->redirect("/index/list");
    }
}
