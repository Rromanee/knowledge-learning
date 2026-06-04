<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for OrderItem entities.
 * Provides methods to check user purchase history.
 *
 * @extends ServiceEntityRepository<OrderItem>
 */
class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    /**
     * Returns order items where the given course was purchased by the given user.
     *
     * @param Course $course The course to check
     * @param User   $user   The user to check
     * @return OrderItem[]
     */
    public function findPurchasedCourseByUser(
        Course $course,
        User $user
        ): array
    {
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
     * @param Lesson $lesson The lesson to check
     * @param User   $user   The user to check
     * @return OrderItem[]
     */
    public function findPurchasedLessonByUser(
        Lesson $lesson,
        User $user
    ): array
    {
        return $this->createQueryBuilder('oi')
            ->join('oi.customerOrder', 'o')
            ->andWhere('oi.lesson = :lesson')
            ->andWhere('o.user = :user')
            ->setParameter('lesson', $lesson)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return OrderItem[] Returns an array of OrderItem objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?OrderItem
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
