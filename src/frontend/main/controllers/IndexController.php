<?php

use Phalcon\Mvc\Controller;
use Phalcon\Http\Cookie;

/**
 * Index Controller
 */
class IndexController extends Controller
{
    public function initialize()
    {
        $this->ip = $this->config->ip;
    }
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
                        $callback = 'http://' . $this->ip . '/frontend/index/callback';
                        $data=[
                            "callback"=>$callback,
                            "email" => $post["email"]
                        ];
                        return $this->response->redirect("/api/register?" . http_build_query($data));
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
     * Callback for token generation
     *
     * @return void
     */
    public function callbackAction()
    {
        $this->session->token = $this->request->getQuery("access_token");
        $this->session->email = $this->request->getQuery("email");
        return $this->response->redirect("/frontend/product");
    }
}
