<?php

namespace VelJo\GHProjectBundle\Twig;

class CutStringExtension extends \Twig_Extension {

    public function getName()
    {
        return 'cut_string';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('cutString', array($this, 'cutString')),
        );
    }

    public function cutString($text, $length = 250)
    {
        $string = substr($text, 0, $length)." ... ";

        return $string;
    }
}