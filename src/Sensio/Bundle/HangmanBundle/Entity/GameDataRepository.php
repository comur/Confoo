<?php

namespace Sensio\Bundle\HangmanBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * GameDataRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GameDataRepository extends EntityRepository
{
    /**
     * Returns the list of the X most recent games.
     *
     * @param integer $limit The number of games to retrieve
     * @return array
     */
    public function getMostRecentGames($limit)
    {
        $q = $this
            ->createQueryBuilder('g')
            ->leftJoin('g.player', 'p')
            ->orderBy('g.startAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
        ;

        return $q->getResult();
    }
}