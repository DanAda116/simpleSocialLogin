<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\Register;
use App\Services\FacebookService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\LoginService;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends Controller
{

    public function index()
    {
        return $this->render('login/index.html.twig');
    }

    /**
     * @Route("/google/", name="google")
     * @param LoginService $login
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function googleLogin(LoginService $login)
    {
        $url = $login->getAuthUrl();

        return $this->redirect($url);
    }

    /**
     *@Route("/auth", name="auth")
     */
    public function googleAuth()
    {
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

    /**
     * @Route("/facebookLogin", name="facebookLogin")
     * @param FacebookService $facebookService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function facebookLoginURL(FacebookService $facebookService)
    {
        $url = $facebookService->getLoginURL();

        return $this->redirect($url);
    }

    /**
     * @Route("/fbcallback", name="fbcallback")
     * @param FacebookService $facebookService
     * @return Response
     */
    public function facebookLogin(FacebookService $facebookService)
    {
        $facebookService->login();
        $profile = $facebookService->getUserData();

        dump($profile);

        return $this->render('login/index.html.twig');
    }
}
