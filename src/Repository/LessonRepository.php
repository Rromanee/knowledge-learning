<?php

namespace App\Repository;

use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for managing Lesson entities.
 *
 * @extends ServiceEntityRepository<Lesson>
 */
class LessonRepository extends ServiceEntityRepository
{
    /**
     * Initializes the Lesson repository.
     *
     * @param ManagerRegistry $registry The Doctrine manager registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    /**
     * Searches lessons by their title.
     *
     * The search is case-insensitive and matches lessons
     * whose title contains the provided search term.
     *
     * @param string $search The search term entered by the user.
     *
     * @return Lesson[] The lessons matching the search criteria.
     */
    public function search(string $search): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('LOWER(l.title) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('l.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
