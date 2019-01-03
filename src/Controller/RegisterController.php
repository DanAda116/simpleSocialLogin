<?php
/**
 * Created by PhpStorm.
 * User: Danielczyk
 * Date: 11.12.2018
 * Time: 15:50
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", methods="POST", name="register", options={"expose"=true})
     */
    public function register(Request $request)
    {
        if($request->isXmlHttpRequest()){
            ;
        }
    }
}