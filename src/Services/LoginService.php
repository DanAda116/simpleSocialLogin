<?php

namespace App\Services;

use Google_Client;

class LoginService
{
    /**
     * @var Google_Client $google_client
     */
    private $google_client;
    private $projectDir;

    /**
     * LoginService constructor.
     * @param Google_Client $google_client
     * @param $projectDir
     * @throws \Google_Exception
     */

    public function __construct(Google_Client $google_client, $projectDir)
    {
            $this->projectDir = $projectDir;

            $this->google_client = $google_client;
            $this->google_client->setAuthConfig($this->projectDir. '/client.json');
            $this->google_client->setAccessType("offline");
            $this->google_client->addScope('profile');
            $this->google_client->setRedirectUri('http://localhost:8000/auth');
    }


    public function getAuthUrl() {
        $auth_url = $this->google_client->createAuthUrl();
        return filter_var($auth_url, FILTER_SANITIZE_URL);
    }
    public function getClient() {

        if(!isset($_SESSION))
            session_start();

        if(isset($_GET['code'])) {

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

    public function getClientInfo(Google_Client $client){
        $infoService = new \Google_Service_Plus($client);
        $info = $infoService->people->get('me');

        $data = [
            'fullname' => $info->getDisplayName(),
            'avatar_img' => $info->getImage()->url
        ];
        dump($info);

        return $data;
    }

    private function saveGoogleUserCredentials(){

//        $this->getClientInfo($this->google_client);
    }
}