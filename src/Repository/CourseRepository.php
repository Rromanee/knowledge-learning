<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for managing Course entities.
 *
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    /**
     * Initializes the Course repository.
     *
     * @param ManagerRegistry $registry The Doctrine manager registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * Searches courses by their title.
     *
     * The search is case-insensitive and matches courses
     * whose title contains the provided search term.
     *
     * @param string $search The search term entered by the user.
     *
     * @return Course[] The courses matching the search criteria.
     */
    public function search(string $search): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('LOWER(c.title) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}