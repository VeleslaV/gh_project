<?php

namespace VelJo\GHProjectBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GbookRepository extends EntityRepository
{
    public function findAllOrderedById()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT g FROM VelJoGHProjectBundle:Gbook g ORDER BY g.id DESC')
            ->getResult();
    }

    public function findLatestCommentsLimit($limit)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT g FROM VelJoGHProjectBundle:Gbook g ORDER BY g.id DESC')
            ->setMaxResults($limit)
            ->getResult();
    }
}
