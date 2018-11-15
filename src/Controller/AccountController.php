<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserAvatar;
use App\Services\UploadService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends BaseController
{
    /**
     * @Route("/account", name="app_account")
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param UploadService $uploadService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, UploadService $uploadService)
    {

        $user = new User();
        $form = $this->createForm(UserAvatar::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine();
            $user = $this->getUser();

            /**
             * @var $file UploadedFile
             */
            $file = $form->get('avatarImage')->getData();

            $fileName = $uploadService->upload($file);

            $user->setAvatarImage($fileName);

            $em->getManager()->persist($user);
            $em->getManager()->flush();

            return $this->redirect($this->generateUrl('app_account'));
        }

        return $this->render('account/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
