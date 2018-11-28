<?php
/**
 * Created by PhpStorm.
 * User: Danielczyk
 * Date: 28.11.2018
 * Time: 13:56
 */

namespace App\DataFixtures;


use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PostsFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for($i = 0 ; $i < 5; $i++){
            $post = new Post();
            $post->setContent("Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam, amet asperiores consequuntur deleniti id 
                               labore magnam minima natus neque optio perferendis placeat quaerat quo quos ratione sunt veritatis voluptas 
                               voluptatum?");
            $post->setCreatedAt(new \DateTime('now'));
            $post->setAuthor($this->getReference(UserFixture::USER_REFERENCE.rand(0,10)));

            $manager->persist($post);
            $manager->flush();
        }


    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            UserFixture::class
        ];
    }
}