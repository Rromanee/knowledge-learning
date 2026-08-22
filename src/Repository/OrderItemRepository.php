<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Theme;
use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for OrderItem entities.
 *
 * Provides methods to check user purchase history
 * and retrieve purchased content.
 *
 * @extends ServiceEntityRepository<OrderItem>
 */
class OrderItemRepository extends ServiceEntityRepository
{
    /**
     * Initializes the OrderItem repository.
     *
     * @param ManagerRegistry $registry The Doctrine manager registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    /**
     * Returns order items where the given course was purchased by the given user.
     *
     * @param Course $course The course to check.
     * @param User   $user   The user to check.
     *
     * @return OrderItem[] The purchased course items.
     */
    public function findPurchasedCourseByUser(
        Course $course,
        User $user
    ): array {
        return $this->createQueryBuilder('oi')
            ->join('oi.customerOrder', 'o')
            ->andWhere('oi.course = :course')
            ->andWhere('o.user = :user')
            ->setParameter('course', $course)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns order items where the given lesson was purchased by the given user.
     *
     * @param Lesson $lesson The lesson to check.
     * @param User   $user   The user to check.
     *
     * @return OrderItem[] The purchased lesson items.
     */
    public function findPurchasedLessonByUser(
        Lesson $lesson,
        User $user
    ): array {
        return $this->createQueryBuilder('oi')
            ->join('oi.customerOrder', 'o')
            ->andWhere('oi.lesson = :lesson')
            ->andWhere('o.user = :user')
            ->setParameter('lesson', $lesson)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns the items purchased by a user within a specific theme.
     *
     * The result can contain purchased courses or lessons.
     *
     * @param User  $user  The user whose purchases should be retrieved.
     * @param Theme $theme The theme used to filter the purchases.
     *
     * @return OrderItem[] The purchased items belonging to the theme.
     */
    public function findPurchasedItemsByUserAndTheme(
        User $user,
        Theme $theme
    ): array {
        return $this->createQueryBuilder('oi')
            ->join('oi.customerOrder', 'o')
            ->leftJoin('oi.course', 'c')
            ->leftJoin('oi.lesson', 'l')
            ->leftJoin('l.course', 'lc')
            ->andWhere('o.user = :user')
            ->andWhere(
                'c.theme = :theme OR lc.theme = :theme'
            )
            ->setParameter('user', $user)
            ->setParameter('theme', $theme)
            ->getQuery()
            ->getResult();
    }
}
