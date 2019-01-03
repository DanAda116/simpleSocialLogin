<?php
/**
 * Created by PhpStorm.
 * User: Danielczyk
 * Date: 03.01.2019
 * Time: 11:04
 */

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class FacebookService
{
    private $fb;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct($appID, $appSecret, EntityManagerInterface $entityManager)
    {
        $this->fb = new Facebook([
            'app_id'                => $appID,
            'app_secret'            => $appSecret,
            'default_graph_version' => 'v2.10',
        ]);
        $this->entityManager = $entityManager;
    }

    public function getLoginURL()
    {
        if(!$_SESSION)
            session_start();

        $helper = $this->fb->getRedirectLoginHelper();
        $permissions = ['email'];

        $loginURL = $helper->getLoginUrl('http://'.$_SERVER['HTTP_HOST'].'/fbcallback', $permissions);

        return $loginURL;
    }

    public function login()
    {
        $helper = $this->fb->getRedirectLoginHelper();


        if(!(isset($_SESSION['facebook_access_token'])))
        {

            try {
                $_SESSION['facebook_access_token'] = $helper->getAccessToken();
            } catch (FacebookSDKException $e) {
                echo $e->getMessage();
            }
        }

        $this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);

    }

    public function getUserData()
    {
        try {
            $profileRequest = $this->fb->get('/me?fields=name,first_name,last_name,email,link,gender,locale,cover,picture');
            $fbUserProfile = $profileRequest->getGraphNode()->asArray();


        } catch (FacebookSDKException $e) {
            echo $e->getMessage();
        }

        return $fbUserProfile;
    }

    public function createUserFromFacebook($data)
    {
        $user = new User();

        $user->setEmail($data['email']);
        $user->setFirstName($data['name']);

        $fileName = UploadService::saveAvatarImage($data['picture']['url']);

        $user->setAvatarImage($fileName);
        $user->setGoogleId($data['id']);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

}