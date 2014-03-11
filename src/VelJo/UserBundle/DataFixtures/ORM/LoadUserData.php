<?php

namespace VelJo\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $users = Yaml::parse($this->getYmlFile());

        foreach ($users['users'] as $value) {
            $user = $userManager->createUser();

            $user
                ->setUsername($value['name'])
                ->setEmail($value['email'])
                ->setPlainPassword($value['pwd'])
                ->setRoles(array($value['role']))
                ->setEnabled(true)
            ;

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/appData/users.yml';
    }
}
?>