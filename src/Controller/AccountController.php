<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends BaseController
{
    /**
     * @Route("/account", name="app_account")
     * @IsGranted("ROLE_USER")
     */
    public function index(LoggerInterface $logger)
    {
        $logger->debug('Checking account page for: '. $this->getUser()->getEmail());

        return $this->render('account/index.html.twig', [
        ]);
    }
}
