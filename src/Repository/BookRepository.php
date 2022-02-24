<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Return latest book's id
     * @return int
     */
    public function getLatestId(): int
    {
        $query = $this->createQueryBuilder('b');
        $query
            ->setMaxResults(1)
            ->orderBy("b.id","DESC");

        $latest = $query->getQuery()->execute();

        if(empty($latest)){
            return 0;
        }
        return $latest[0]->getId();
    }

    public function getAllOrderByCategory()
    {
        $query = $this->createQueryBuilder("b");
        $query
            ->orderBy("b.category", "ASC");

        return $query->getQuery()->execute();
    }
}
