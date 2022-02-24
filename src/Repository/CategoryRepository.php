<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Return latest category's id
     * @return int
     */
    public function getLatestId(): int
    {
        $query = $this->createQueryBuilder('c');
        $query
            ->setMaxResults(1)
            ->orderBy("c.id","DESC");

        $latest = $query->getQuery()->execute();

        if(empty($latest)){
            return 0;
        }
        return $latest[0]->getId();
    }

    /**
     * Return random Category
     * @return mixed
     */
    public function getRandom(): Category
    {
        $query = $this->createQueryBuilder("c");
        $shelfs = $query->getQuery()->execute();

        return $shelfs[array_rand($shelfs)];
    }

    /**
     * Return all category order by shelf id
     * @return float|int|mixed|string
     */
    public function findAllOrderByShelfId(): mixed
    {
        $query = $this->createQueryBuilder("c");
        $query
            ->orderBy("c.Shelf", "ASC");
        return $query->getQuery()->execute();
    }
}
