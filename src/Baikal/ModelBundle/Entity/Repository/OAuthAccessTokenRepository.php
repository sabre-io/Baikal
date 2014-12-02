<?php

namespace Baikal\ModelBundle\Entity\Repository;

use Doctrine\ORM\EntityManager;

use Baikal\ModelBundle\Entity\OAuthClient;

class OAuthAccessTokenRepository {
    protected $em;
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function findAll() {
        $query = $this->em->createQuery('SELECT o FROM BaikalModelBundle:OAuthAccessToken o');
        return $query->getResult();
    }

    public function findByClient(OAuthClient $client) {
        return $this
            ->qb_findByClient($client)
            ->getQuery()
            ->getResult();
    }

    public function qb_findByClient(OAuthClient $client) {

        $qb = $this->em->createQueryBuilder()
            ->select('o')
            ->from('BaikalModelBundle:OAuthAccessToken', 'o')
            ->where('o.client = :client')
            ->setParameter('client', $client);

        return $qb;
    }

    public function countForClient(OAuthClient $client) {
        if(is_null($client->getId())) return 0;
        
        return intval($this->qb_countForClient($client)
            ->getQuery()
            ->getSingleScalarResult());
    }

    public function qb_countForClient(OAuthClient $client) {

        return $this
            ->qb_findByClient($client)
            ->select('count(o)');
    }
}