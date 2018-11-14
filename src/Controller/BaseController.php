<?php
/**
 * Created by PhpStorm.
 * User: Danielczyk
 * Date: 14.11.2018
 * Time: 12:51
 */

namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    protected function getUser():User
    {
        return parent::getUser();
    }
}