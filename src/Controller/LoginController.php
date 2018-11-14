<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\LoginService;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{

    public function index(LoginService $login)
    {

        $info = [];

        if(isset($_SESSION['token'])) {
            $client = $login->getClient();
            $login->getRestInfo($client);
            $login->getClientInfo($client);
            dd($this->getUser()->getFirstName());
        }



        return $this->render('login/index.html.twig');
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
        $client = $login->getClient();
        $login->getClientInfo($client);

        return $this->render('login/loader.html.twig', [
            'host' => $_SERVER['HTTP_HOST']
        ]);
    }

    /**
     *@Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new Exception('Something went wrong');
    }

    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('login/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
            'data' => []
        ]);
    }
}
