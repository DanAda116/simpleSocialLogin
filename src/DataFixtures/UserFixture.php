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
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    public const USER_REFERENCE = 'user-fixtures-reference';

    /**
     * @var UserPasswordEncoderInterface
     */

    public $usersFixtures;

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
            $user->setRoles($user->getRoles());
            $user->setFirstName($this->names[rand(1, (sizeof($this->names)))-1]);
            $user->setPassword($this->userPasswordEncoder->encodePassword(
               $user,
               'test'
            ));
            $user->setAvatarImage('default_profile_avatar.png');


            $manager->persist($user);
            $manager->flush();

            $this->addReference(self::USER_REFERENCE.$i, $user);
        }

    }


}