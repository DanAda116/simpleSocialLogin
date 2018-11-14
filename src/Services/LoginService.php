<?php

namespace App\Services;

use App\Entity\User;
use Google_Client;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginService
{
    /**
     * @var Google_Client $google_client
     */
    private $google_client;
    private $projectDir;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * LoginService constructor.
     * @param Google_Client $google_client
     * @param $projectDir
     * @throws \Google_Exception
     */

    public function __construct(Google_Client $google_client, $projectDir, TokenStorageInterface $tokenStorage, SessionInterface $session, EventDispatcherInterface $eventDispatcher, RequestStack $request, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->projectDir = $projectDir;

        $this->google_client = $google_client;
        $this->google_client->setAuthConfig($this->projectDir . '/client.json');
        $this->google_client->setAccessType("offline");
        $this->google_client->addScope(array([
            'profile',
            'email'
        ]));
        $this->google_client->setRedirectUri('http://localhost:8000/auth');


        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
        $this->eventDispatcher = $eventDispatcher;
        $this->request = $request->getCurrentRequest();
        $this->csrfTokenManager = $csrfTokenManager;
    }


    public function getAuthUrl()
    {
        $auth_url = $this->google_client->createAuthUrl();
        return filter_var($auth_url, FILTER_SANITIZE_URL);
    }

    public function getClient()
    {

        if (!isset($_SESSION))
            session_start();

        if (isset($_GET['code'])) {

            $token = $this->google_client->fetchAccessTokenWithAuthCode($_GET['code']);

            $this->google_client->setAccessToken($token);

            if ($this->google_client->isAccessTokenExpired()) {
                echo 'expired';
                if ($this->google_client->getRefreshToken()) {
                    $this->google_client->fetchAccessTokenWithRefreshToken($this->google_client->getRefreshToken());
                }
            } else
                echo 'ok';

        } else
            $this->google_client->setAccessToken($_SESSION['token']);

        $_SESSION['token'] = $this->google_client->getAccessToken();

        return $this->google_client;
    }

    public function getClientInfo(Google_Client $client)
    {

        $infoService = new \Google_Service_Plus($client);
        $info = $infoService->people->get('me');

        $data = [
            'fullname' => $info->getDisplayName(),
            'avatar_img' => $info->getImage()->url,
            'email' => $info->getEmails()[0]->value
        ];

        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['fullname']);
        $user->setAvatarImage($data['avatar_img']);

        $token = new UsernamePasswordToken($user, $data, 'main', $user->getRoles());

        $this->tokenStorage->setToken($this->csrfTokenManager->getToken('example')->getId(), $token);

        $this->session->set('_security_main', serialize($token));


        $this->eventDispatcher->dispatch("security.interactive_login", new InteractiveLoginEvent($this->request, $token));
    }
}


