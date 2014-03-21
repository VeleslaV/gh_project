<?php

namespace VelJo\GHProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;

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

    public function newsAction()
    {
        $pageData = $this->createPageObject("news");

        return $this->render('VelJoGHProjectBundle::main.html.twig', array('pageData' => $pageData));
    }

    public function aboutAction()
    {
        $pageData = $this->createPageObject("about");

        return $this->render('VelJoGHProjectBundle::about.html.twig', array('pageData' => $pageData));
    }

    public function contactAction()
    {
        $pageData = $this->createPageObject("contact");

        return $this->render('VelJoGHProjectBundle::contact.html.twig', array('pageData' => $pageData));
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Search >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function searchFormAction(Request $request)
    {
        $pageData = array();

        $form = $this->createFormBuilder()
            ->add('keyword', 'text', array(
                'constraints' => array(
                    new NotBlank(),
                    new Length(array('min' => 3)),
                ),
            ))
            ->getForm();

        $form->handleRequest($request);
        $pageData['form'] = $form->createView();

        if ($form->isValid()) {
            $data = $form->getData();
            $keyword = $data['keyword'];

            return $this->redirect($this->generateUrl('_search_keyword', array('keyword' => $keyword), true));
        }

        return $this->render('VelJoGHProjectBundle:Modules:search_form.html.twig', $pageData);
    }

    public function searchResultAction($keyword = "")
    {
        $pageData = array();

        if(empty($keyword)){
            $pageData['resultData']['error'] = "No keyword for search =(";
        }else{
            $pageData['kwd'] = $keyword;
            $keyword = preg_replace('/\s/','+',$keyword);

            $target_url = "http://www.lastfm.ru/music/".$keyword."/+albums";
            $elementAddress = "section[class=album-item]";

            $targetDom = HtmlDomParser::file_get_html($target_url);
            if(isset($targetDom)){
                $domElements = $targetDom->find($elementAddress);

                $elementsData = array();
                foreach($domElements as $key => $element) {
                    $elementsData['albums'][$key]['name'] = $element->children(3)->children(0)->children(0)->children(0)->innertext;
                    $elementsData['albums'][$key]['img'] = $element->children(2)->children(0)->src;
                }
                $pageData['parseResult'] = $elementsData;
            }
        }

        return $this->render('VelJoGHProjectBundle::search.html.twig', $pageData);
    }
}
