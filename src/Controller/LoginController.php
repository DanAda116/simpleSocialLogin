<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\LoginService;

class LoginController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index(LoginService $login)
    {

        $info = [];

        if(isset($_SESSION['token'])) {
            $client = $login->getClient();
            $info = $login->getClientInfo($client);
        }


        return $this->render('login/index.html.twig', [
            'data' => $info
        ]);
    }

    /**
     *@Route("/google/", name="google")
    */
    public function googleLogin(LoginService $login)
    {
        $url = $login->getAuthUrl();

        return $this->redirect($url);
    }

    /**
     *@Route("/auth", name="auth")
     */
    public function googleAuth(LoginService $login)
    {
        $login->getClient();

        return $this->render('login/loader.html.twig', [
            'host' => $_SERVER['HTTP_HOST']
        ]);
    }

    /**
     *@Route("/logout", name="logout")
     */
    public function logout()
    {
        if(isset($_SESSION))
            session_destroy();

        return $this->redirectToRoute('home');
    }
}
