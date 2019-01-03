<?php
/**
 * Created by PhpStorm.
 * User: Danielczyk
 * Date: 03.01.2019
 * Time: 11:04
 */

namespace App\Services;




use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class FacebookService
{
    private $fb;

    public function __construct($appID, $appSecret)
    {
        $this->fb = new Facebook([
            'app_id'                => $appID,
            'app_secret'            => $appSecret,
            'default_graph_version' => 'v2.10',
        ]);
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

        if(!isset($_SESSION['facebook_access_token']))
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


}