<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        if ($this->request->isPost()) {
            return $this->response->redirect("/index/list");
        }
    }
    public function listAction()
    {
        $token = $this->session->token;
        $ip='192.168.2.6'; //server ip address
        //
        // curl to get orders;
        //
        $url = "http://$ip:8080/api/orders/get?access_token=$token";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        curl_close($curl);
        //
        // curl to get user email
        //
        $url = "http://$ip:8080/api/user/email?access_token=$token";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $email = curl_exec($curl);
        curl_close($curl);
        $this->view->orders=json_decode($data,1);
        $this->view->email=$email;
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
