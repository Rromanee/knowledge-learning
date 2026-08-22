<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for managing Theme entities.
 *
 * @extends ServiceEntityRepository<Theme>
 */
class ThemeRepository extends ServiceEntityRepository
{
    /**
     * Initializes the Theme repository.
     *
     * @param ManagerRegistry $registry The Doctrine manager registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    /**
     * Searches themes by their title.
     *
     * The search is case-insensitive and matches themes
     * whose title contains the provided search term.
     *
     * @param string $search The search term entered by the user.
     *
     * @return Theme[] The themes matching the search criteria.
     */
    public function search(string $search): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('LOWER(t.title) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('t.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
