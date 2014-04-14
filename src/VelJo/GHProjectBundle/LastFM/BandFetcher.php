<?php

namespace VelJo\GHProjectBundle\LastFM;

use Doctrine\ORM\EntityManager;
use Sunra\PhpSimple\HtmlDomParser;
use VelJo\GHProjectBundle\Entity\Band;

class BandFetcher
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $bandName
     * @return Band
     */
    public function fetch($bandName)
    {
        $bandName = $this->normalizeBandName($bandName);
        $band = $this->em->getRepository('VelJoGHProjectBundle:Band')->findOneBy(array('name' => $bandName));

        if (!$band) {
            $band = new Band();
            $band->setName($bandName);
        }

        $bandUpdatedAt = $band->getUpdatedAt();

        if (!$bandUpdatedAt || $bandUpdatedAt->diff(new \DateTime('-1 day'))->d >= 1) {
            $this->refreshBand($band);
        }

        return $band;
    }

    /**
     * @param Band $band
     */
    private function refreshBand(Band $band)
    {
        $lastFMName = preg_replace('/\s/', '+', $band->getName());
        $infoDom = HtmlDomParser::file_get_html("http://www.lastfm.ru/music/{$lastFMName}/+wiki");

        if ($infoDom) {
            $band->setDescription($infoDom->find("div[id=wiki]", 0)->innertext);
        }

        $photosDom = HtmlDomParser::file_get_html("http://www.lastfm.ru/music/{$lastFMName}/+images");

        if ($photosDom) {
            $photos = array();

            foreach($photosDom->find("ul[id=pictures] li a[class=pic] img") as $photoElement) {
                $photos[] = $photoElement->src;
            }

            $band->setPhotos($photos);
        }

        $albumsDom = HtmlDomParser::file_get_html("http://www.lastfm.ru/music/{$lastFMName}/+albums");

        if ($albumsDom) {
            $albums = array();

            foreach($albumsDom->find("section[class=album-item]") as $aelement) {
                $albumSimpleName = $aelement->children(3)->children(0)->children(0)->children(0)->innertext;
                $albums[] = array(
                    'name' => preg_replace('/\/\s/','',$albumSimpleName),
                    'img' => $aelement->children(2)->children(0)->src,
                );
            }

            $band->setAlbums($albums);
        }

        $band->setUpdatedAt(new \DateTime());
        $this->em->persist($band);
        $this->em->flush();
    }

    /**
     * @param $bandName
     * @return string
     */
    private function normalizeBandName($bandName)
    {
        return trim($bandName);
    }
}
