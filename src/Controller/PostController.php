<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="post")
     * @IsGranted("ROLE_USER")
     */
    public function index()
    {
        return $this->render('post/index.html.twig', [
            'data' => [],
        ]);
    }
}
