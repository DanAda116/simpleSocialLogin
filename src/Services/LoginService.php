<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Google_Client;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class LoginService
{
    /**
     * @var Google_Client $google_client
     */
    private $google_client;
    private $projectDir;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var UploadService
     */
    private $uploadService;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * LoginService constructor.
     * @param Google_Client $google_client
     * @param $projectDir
     * @param EntityManagerInterface $entityManager
     * @param UploadService $uploadService
     * @param ParameterBagInterface $parameterBag
     * @throws \Google_Exception
     */

    public function __construct(Google_Client $google_client, $projectDir, EntityManagerInterface $entityManager, UploadService $uploadService, ParameterBagInterface $parameterBag)
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
        $this->entityManager = $entityManager;
        $this->uploadService = $uploadService;
        $this->parameterBag = $parameterBag;
    }


    public function getAuthUrl()
    {
        $auth_url = $this->google_client->createAuthUrl();
        return filter_var($auth_url, FILTER_SANITIZE_URL);
    }

    public function getClient()
    {
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
            'email' => $info->getEmails()[0]->value,
            'id' => $info->getId()
        ];

        return $data;
    }

    public function createUserFromGoogle($data)
    {
        $user = new User();

        $user->setEmail($data['email']);
        $user->setFirstName($data['fullname']);

        $fileName = $this->saveAvatarImage($data['avatar_img']);

        $user->setAvatarImage($fileName);
        $user->setGoogleId($data['id']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function saveAvatarImage($url)
    {
        $content = file_get_contents($url);

        $fileName = md5(uniqid()).'.jpg';
        $file = $this->parameterBag->get('avatarDirectory').'/'.$fileName;
        $fp = fopen($file, "w");
        fwrite($fp, $content);
        fclose($fp);

        return $fileName;
    }
}


