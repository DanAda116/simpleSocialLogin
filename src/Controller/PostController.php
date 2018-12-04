<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostAdd;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(Request $request, PaginatorInterface $paginator)
    {

        $post = new Post();

        $formAddPost = $this->createForm(PostAdd::class, $post, array(
            'action' => $this->generateUrl('post_POST')
        ));

        $repo = $this->getDoctrine()->getRepository(Post::class);

        $query = $repo->findAllGetQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('post/index.html.twig', [
            'posts' => $pagination,
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
