<?php

namespace VelJo\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VelJoUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
