<?php
namespace Application\Sonata\UserBundle\UserProvider;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Sonata\UserBundle\Model\UserManagerInterface;

abstract class AbstractSocialNetworkProvider
{

    protected $response;

    protected $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    abstract public function setUserData(UserResponseInterface $response);
} 