<?php

use Phalcon\Mvc\Controller;

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
                        $this->session->email=$post['email'];
                        $callback = urlencode('http://192.168.2.6:8080/frontend/index/callback');
                        return $this->response->redirect("/api/register?callback=$callback");
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
        $this->session->token=$this->request->getQuery("access_token");
        return $this->response->redirect("/frontend/product");
    }
}
