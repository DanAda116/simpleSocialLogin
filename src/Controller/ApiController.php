<?php
/**
 * Created by PhpStorm.
 * User: Danielczyk
 * Date: 13.12.2018
 * Time: 13:30
 */

namespace App\Controller;


use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;



class ApiController extends AbstractController
{
    /**
     * @Route("/api/posts")
     */
    public function getPosts()
    {

        $em = $this->getDoctrine()->getRepository(Post::class);

        $query = $em->findAllGetQuery();

        $posts = $query->getArrayResult();

        return new JsonResponse($posts);
    }
}