<?php

namespace VelJo\GHProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use VelJo\GHProjectBundle\Entity\Gbook;
use VelJo\GHProjectBundle\Entity\Article;
use VelJo\GHProjectBundle\Form\Type\GbookType;
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

        $repository = $this->getDoctrine()->getRepository('VelJoGHProjectBundle:Article');
        $articlesObj = $repository->findLatestArticlesLimit("3");

        if(empty($articlesObj)){
            $pageData['error'] = "No articles found =(";
        }else{
            $pageData['articles'] = $articlesObj;
        }

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
            $band = $data['keyword'];

            return $this->redirect($this->generateUrl('_search_band', array('band' => $band), true));
        }

        return $this->render('VelJoGHProjectBundle:Modules:search_form.html.twig', $pageData);
    }

    public function searchBandResultAction($band = "")
    {
        $pageData = array();

        if(empty($band)){
            $pageData['resultData']['error'] = "No keyword for search =(";
        }else{
            $elementsData = array();
            $pageData['band'] = $band;
            $band = preg_replace('/\s/','+',$band);

            $infoUrl = "http://www.lastfm.ru/music/".$band."/+wiki";
            $infoDom = HtmlDomParser::file_get_html($infoUrl);
            if(isset($infoDom)){
                $bandInfo = $infoDom->find("div[id=wiki]", 0)->innertext;
            }

            $photosUrl = "http://www.lastfm.ru/music/".$band."/+images";
            $photosDom = HtmlDomParser::file_get_html($photosUrl);
            if(isset($photosDom)){
                $photosDomElements = $photosDom->find("ul[id=pictures] li a[class=pic] img");
                foreach($photosDomElements as $phkey => $phelement) {
                    $elementsData['photos'][$phkey]['src'] = $phelement->src;
                }
            }

            $albumsUrl = "http://www.lastfm.ru/music/".$band."/+albums";
            $albumsDom = HtmlDomParser::file_get_html($albumsUrl);
            if(isset($albumsDom)){
                $albumsDomElements = $albumsDom->find("section[class=album-item]");
                foreach($albumsDomElements as $akey => $aelement) {
                    $albumSimpleName = $aelement->children(3)->children(0)->children(0)->children(0)->innertext;
                    $elementsData['albums'][$akey]['name'] = preg_replace('/\/\s/','',$albumSimpleName);
                    $elementsData['albums'][$akey]['img'] = $aelement->children(2)->children(0)->src;
                }
            }

            $elementsData['info'] = $bandInfo;
            $pageData['parseResult'] = $elementsData;
        }

        return $this->render('VelJoGHProjectBundle::search.html.twig', $pageData);
    }

    public function searchAlbumResultAction($band = "", $album = "")
    {
        $pageData = array();

        if(empty($band) or empty($album)){
            $pageData['resultData']['error'] = "Empty band or album keyword for search =(";
        }else{
            $pageData['band'] = $band;
            $pageData['album'] = $album;
            $band = preg_replace('/\s/','+',$band);
            $album = preg_replace('/\s/','+',$album);

            $target_url = "http://www.last.fm/music/".$band."/".$album;

            $targetDom = HtmlDomParser::file_get_html($target_url);
            if(isset($targetDom)){
                $elementsData = array();
                $albumSrc = $targetDom->find("div[class=album-cover-wrapper] a img[class=album-cover]", 0)->src;

                $tagElements = $targetDom->find("section[class=global-tags] li a[rel=tag]");
                foreach($tagElements as $tkey => $telement) {
                    $elementsData['tags'][$tkey]['name'] = $telement->innertext;
                }

                $songElements = $targetDom->find("table[id=albumTracklist] tbody tr td[class=subjectCell] a span");
                foreach($songElements as $skey => $selement) {
                    $elementsData['songs'][$skey]['name'] = $selement->innertext;
                }

                $elementsData['info']['img'] = $albumSrc;
                $pageData['parseResult'] = $elementsData;
            }
        }

        return $this->render('VelJoGHProjectBundle::search.html.twig', $pageData);
    }

    public function searchSongResultAction($band = "", $album = "", $song = "")
    {
        $pageData = array();

        if(empty($band) or empty($album) or empty($song)){
            $pageData['resultData']['error'] = "Empty band or album keyword for search =(";
        }else{
            $pageData['band'] = $band;
            $pageData['album'] = $album;
            $pageData['song'] = $song;
            $band = preg_replace('/\s/','+',$band);
            $album = preg_replace('/\s/','+',$album);
            $song = preg_replace('/\s/','+',$song);

            $target_url = "http://www.last.fm/music/".$band."/".$album."/".$song;

            $targetDom = HtmlDomParser::file_get_html($target_url);
            if(isset($targetDom)){
                $elementsData = array();

                $pageData['parseResult'] = $elementsData;
            }
        }

        return $this->render('VelJoGHProjectBundle::search.html.twig', $pageData);
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Gbook >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

    public function gbookAction(Request $request)
    {
        $pageData = $this->createPageObject("gbook");
        $commentsQuery = $this->getCommentsData();

        $paginator  = $this->get('knp_paginator');
        $p_options = Yaml::parse($this->getYmlFile());

        $pagination = $paginator->paginate(
            $commentsQuery,
            $this->get('request')->query->get('page', $p_options['start_from']),$p_options['posts_per_page']
        );

        $pageData['pagination'] = $pagination;

        $gbook = new Gbook();
        $form = $this->createForm(new GbookType(), $gbook);
        $form->handleRequest($request);
        $pageData['form'] = $form->createView();

        if ($form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($gbook);
            $manager->flush();

            return $this->redirect($this->generateUrl('_gbook'));
        }

        return $this->render('VelJoGHProjectBundle::gbook.html.twig', $pageData);
    }

    public function oneCommentAction($cid = "")
    {
        $pageData = $this->createPageObject("comment");

        if($cid == ""){
            $pageData['resultData']['error'] = "No comment id =(";
        }else{
            $pageData['resultData'] = $this->getCommentsData($cid);
        }

        return $this->render('VelJoGHProjectBundle::comment.html.twig', $pageData);
    }

    public function getCommentsData($cid = "")
    {
        if($cid == ""){
            $repository = $this->getDoctrine()->getRepository('VelJoGHProjectBundle:Gbook');
            $query = $repository->createQueryBuilder('g')
                ->orderBy('g.id', 'DESC')
                ->getQuery();

            $commentObj = $query;
        }else{
            $repository = $this->getDoctrine()->getRepository('VelJoGHProjectBundle:Gbook');
            $commentObj = $repository->find($cid);
        }

        if (!$commentObj) {
            throw $this->createNotFoundException('No comments found =(');
        }

        return $commentObj;
    }
    
    public function getCommentsCountAction($aid)
    {
        $thread = $this->container
            ->get('fos_comment.manager.thread')
            ->findThreadById($aid);
        $numOfComments = is_object($thread) ? $thread->getNumComments() : 0;

        return new Response($numOfComments);
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Articles >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

    public function oneArticleAction($aid = "")
    {
        $pageData = array();

        if(empty($aid)){
            $pageData['resultData']['error'] = "No article id =(";
        }else{
            $pageData['resultData']['article'] = $this->getArticleData($aid);
        }

        return $this->render('VelJoGHProjectBundle::article.html.twig', $pageData);
    }

    public function getArticleData($aid)
    {
        $repository = $this->getDoctrine()->getRepository('VelJoGHProjectBundle:Article');

        if(empty($aid)){
            throw $this->createNotFoundException('No articles found =(');
        }else{
            $articleObj = $repository->find($aid);
        }

        return $articleObj;
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Categories >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function oneCategoryAction($catid = "")
    {
        $pageData = $this->createPageObject("category");

        if(empty($catid)){
            $pageData['resultData']['error'] = "No category id =(";
        }else{
            $pageData['resultData'] = $this->getCategoryData($catid);
        }

        return $this->render('VelJoGHProjectBundle::category.html.twig', $pageData);
    }

    public function getCategoryData($catid)
    {
        $repository = $this->getDoctrine()->getRepository('VelJoGHProjectBundle:Category');

        if(empty($catid)){
            throw $this->createNotFoundException('No categories found =(');
        }else{
            $categoryObj = $repository->findBy(array('name' => $catid), array('id' => 'DESC'));
        }

        return $categoryObj;
    }

    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Tags >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function oneTagAction($tid = "")
    {
        $pageData = $this->createPageObject("tag");

        if(empty($tid)){
            $pageData['resultData']['error'] = "No tag id =(";
        }else{
            $pageData['resultData'] = $this->getTagData($tid);
        }

        return $this->render('VelJoGHProjectBundle::tag.html.twig', $pageData);
    }

    public function getTagData($tid)
    {
        $repository = $this->getDoctrine()->getRepository('VelJoGHProjectBundle:Tag');

        if(empty($tid)){
            throw $this->createNotFoundException('No tags found =(');
        }else{
            $tagObj = $repository->findBy(array('name' => $tid), array('id' => 'DESC'));
        }

        return $tagObj;
    }
    
    //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< Tech >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
    public function successAddAction()
    {
        $pageData = $this->createPageObject("success");
        return $this->render('VelJoGHProjectBundle::success.html.twig', $pageData);
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/../Resources/config/paginator.yml';
    }


}
