<?php
namespace Application\Sonata\UserBundle\UserProvider;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;


class FacebookProvider extends AbstractSocialNetworkProvider
{
    public function setUserData(UserResponseInterface $response)
    {
        $infoAboutUser = $response->getResponse();

        $id = $infoAboutUser['id'];
        $email = $infoAboutUser['email'];
        $firstname = $infoAboutUser['first_name'];
        $lastname = $infoAboutUser['last_name'];
        $username = $firstname.' '.$lastname;
        $gender = $infoAboutUser['gender'];
        $token = $response->getAccessToken();

        $user = $this->userManager->createUser();

        $user->setFacebookUid($id);
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setFirstName($firstname);
        $user->setLastName($lastname);
        $user->setFacebookAccessToken($token);
        $user->setGender($gender);
        $user->setEnabled(true);
        $user->setPassword($username);
        $this->userManager->updateUser($user);

    }
}