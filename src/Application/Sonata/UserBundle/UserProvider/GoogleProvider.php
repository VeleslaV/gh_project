<?php

namespace Application\Sonata\UserBundle\UserProvider;


use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

class GoogleProvider extends AbstractSocialNetworkProvider
{
    public function setUserData(UserResponseInterface $response)
    {
        $infoAboutUser = $response->getResponse();

        $id = $infoAboutUser['id'];
        $email = $infoAboutUser['email'];
//        $verifiedEmail = $infoAboutUser[true];
        $firstname = $infoAboutUser['given_name'];
        $lastname = $infoAboutUser['family_name'];
        $username = $firstname.' '.$lastname;
        $gender = $infoAboutUser['gender'];
        $token = $response->getAccessToken();

        $user = $this->userManager->createUser();

        $user->setGoogleUid($id);
        $user->setEmail($email);
//        $user->setVerifiedEmail($verifiedEmail);
        $user->setUsername($username);
        $user->setFirstName($firstname);
        $user->setLastName($lastname);
        $user->setGoogleAccessToken($token);
        $user->setGender($gender);
        $user->setEnabled(true);
        $user->setPassword($username);
        $this->userManager->updateUser($user);
    }
} 