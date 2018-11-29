<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostAdd;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        $post = new Post();

        $formAddPost = $this->createForm(PostAdd::class, $post, array(
            'action' => $this->generateUrl('post_POST')
        ));

        $posts= $repo->findAll();
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'addPostForm' => $formAddPost->createView()
        ]);
    }

    /**
     * @Route("/new", name="post_POST", methods="POST")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addPost(Request $request)
    {
        $post = new Post();

        $form = $this->createForm(PostAdd::class, $post);
        $form->handleRequest($request);

        if($form->isValid() && $form->isSubmitted()){
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();

            $post->setAuthor($user);
            $post->setCreatedAt(new \DateTime('now'));

            $em->persist($post);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('post'));

    }
}
