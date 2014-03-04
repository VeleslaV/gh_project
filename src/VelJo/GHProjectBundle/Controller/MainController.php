<?php

namespace VelJo\GHProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class MainController extends Controller
{
    protected function createPageObject($pageLink)
    {
        $pageData = array(
            'sectionTitle' => $pageLink,
            'sectionBody' => $pageLink." body"
        );

        return $pageData;
    }

    public function mainAction()
    {
        $pageData = $this->createPageObject("main");

        return $this->render('VelJoGHProjectBundle::main.html.twig', array('pageData' => $pageData));
    }
}
