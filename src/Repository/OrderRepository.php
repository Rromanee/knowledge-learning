<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for managing Order entities.
 *
 * Provides methods to retrieve orders belonging to users.
 *
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * Initializes the Order repository.
     *
     * @param ManagerRegistry $registry The Doctrine manager registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Returns all orders belonging to the given user.
     *
     * Orders are sorted from the most recent to the oldest.
     *
     * @param User $user The user whose orders should be retrieved.
     *
     * @return Order[] The user's orders.
     */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
