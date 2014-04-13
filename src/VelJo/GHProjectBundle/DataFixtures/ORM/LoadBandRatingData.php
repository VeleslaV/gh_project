<?php

namespace VelJo\GHProjectBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Yaml;
use VelJo\GHProjectBundle\Entity\BandRating;

class LoadBandRatingData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $bands = Yaml::parse($this->getYmlFile());

        foreach ($bands['bands_rating'] as $key => $value) {
            $band_rating = new BandRating();

            $band_rating
                ->setBand($value['band'])
                ->setRating($value['rating'])
            ;

            $manager->persist($band_rating);
            $this->addReference($key, $band_rating);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }

    protected function getYmlFile()
    {
        return __DIR__ . '/appData/bands_rating.yml';
    }
}
?>