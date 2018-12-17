<?php

namespace App\Repository;

use App\Entity\Todo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Todo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Todo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Todo[]    findAll()
 * @method Todo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TodoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Todo::class);
    }
    
    
    /**
     * Filter the list of Todos by the given searchterm (respected fields are title, description and owner)
     *
     * @param string $searchterm
     * @return
     * @author Markus Buscher
     */
    public function findBySearchterm(string $searchterm)
    {
        return $this->createQueryBuilder("t")
            ->orWhere('t.title LIKE :searchterm')
            ->orWhere('t.description LIKE :searchterm')
            ->orWhere('t.owner LIKE :searchterm')
            ->setParameter('searchterm', '%'.$searchterm.'%')
            ->getQuery()
            ->getResult();
    }
}
