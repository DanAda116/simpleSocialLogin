<?php

namespace App\Controller;

use App\Entity\Post;
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
        $repo = $this->getDoctrine()->getRepository(Post::class);

        $posts= $repo->findAll();
        return $this->render('post/index.html.twig', [
            'posts' => $posts
        ]);
    }
}
