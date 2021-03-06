<?php

namespace AppBundle\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * CompetitionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CompetitionRepository extends EntityRepository
{
    /**
     * @param array $options
     *
     * @return mixed
     */
    public function findAllOrderedByDate(array $options = [])
    {
        $defaultOptions = [
            'from'  => null,
            'to'    => null,
            'order' => 'DESC',
            'limit' => null,
            'assoc' => false,
        ];
        $options = array_merge($defaultOptions, $options);

        $whereDateIntervalClause = [];

        if (null !== $options['from']) {
            $whereDateIntervalClause[] = "c.startAt >= '{$options['from']}'";
        }

        if (null !== $options['to']) {
            $whereDateIntervalClause[] = "c.startAt <= '{$options['to']}'";
        }

        $whereDateIntervalClause = implode(' AND ', $whereDateIntervalClause);
        $whereDateIntervalClause = ('' === $whereDateIntervalClause) ? $whereDateIntervalClause : ' AND ' . $whereDateIntervalClause ;

        $dql = "SELECT c, htm, atm FROM AppBundle:Competition c
            LEFT JOIN c.homeTeam htm
            LEFT JOIN c.awayTeam atm
            WHERE (c.homeTeam = htm.id OR c.awayTeam = atm.id)
            $whereDateIntervalClause
            ORDER BY c.startAt {$options['order']}";

        $builder = $this->getEntityManager()
            ->createQuery($dql)
            ->setMaxResults($options['limit']);

        // Return results as assoc array or as array of objects
        if ($options['assoc']) {
            $result = $builder->getArrayResult();
        } else {
            $result = $builder->getResult();
        }

        return $result;
    }

    /**
     * @param string $keyword
     *
     * @return mixed
     */
    public function findByTeamNameKeyword(string $keyword)
    {
        $dql = 'SELECT c, htm, atm FROM AppBundle:Competition c
            LEFT JOIN c.homeTeam htm
            LEFT JOIN c.awayTeam atm
            WHERE (c.homeTeam = htm.id OR c.awayTeam = atm.id) AND (htm.name LIKE :keyword OR atm.name LIKE :keyword)
            ORDER BY c.startAt DESC';

        $result = $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getResult();

        return $result;
    }
}
