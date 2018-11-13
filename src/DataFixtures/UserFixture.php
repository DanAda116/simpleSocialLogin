<?php
/**
 * Created by PhpStorm.
 * User: Danielczyk
 * Date: 13.11.2018
 * Time: 10:32
 */

namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    private $names = [
        'Dans',
        'Peter',
        'Sophia',
        'Tom',
        'George',
        'Mike',
        'Tonny'
    ];

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {

        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 10; $i++){
            $user = new User();
            $user->setEmail('random'.$i.'@test.com');
            $user->setFirstName($this->names[rand(0,sizeof($this->names))]);
            $user->setPassword($this->userPasswordEncoder->encodePassword(
               $user,
               'test'
            ));
            $manager->persist($user);
        }
        $manager->flush();
    }
}