<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserAvatar;
use App\Form\UserPassword;
use App\Services\UploadService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends BaseController
{
    /**
     * @Route("/account", name="app_account", methods="GET")
     * @IsGranted("ROLE_USER")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {

        $user = new User();

        $formAvatar = $this->createForm(UserAvatar::class, $user, array(
            'action' => $this->generateUrl('post_avatar')
        ));
        $formPassword=  $this->createForm(UserPassword::class, $user, array(
            'action' => $this->generateUrl('post_pass')
        ));

        return $this->render('account/index.html.twig', [
            'formAvatar' => $formAvatar->createView(),
            'formPassword' => $formPassword->createView()
        ]);
    }

    /**
     * @Route("/account/pass", name="post_pass", methods="POST")
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function passwordPostForm(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserPassword::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine();
            $user = $this->getUser();

            $em->getManager()->persist($user);
            $em->getManager()->flush();

            $this->addFlash(
                'passwordChangeSuccess',
                'You have successfully changed your password'
            );

        } else {
            $errorMessage = $this->getErrorsFromForm($form['password'])[0];
            $this->addFlash(
                'error',
                $errorMessage
            );
        }

        return $this->redirect($this->generateUrl('app_account', array(
            'activeTab' => 'nav-security-tab'
        )));

    }

    /**
     * @Route("/account/avatar", name="post_avatar", methods="POST")
     * @param Request $request
     * @param UploadService $uploadService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function avatarPostForm(Request $request, UploadService $uploadService)
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

        }

        return $this->redirect($this->generateUrl('app_account'));
    }

    /**
     * @Security("is_authenticated()")
     * @Route("/api/account", name="api_account")
     */
    public function accountAPI()
    {

        $user = $this->getUser();

        return $this->json($user, 200, [], [
            'groups' => ['main']
        ]);
    }

    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
