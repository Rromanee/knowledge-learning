<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\User;
use App\Entity\LessonValidation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for LessonValidation entities.
 *
 * @extends ServiceEntityRepository<LessonValidation>
 */
class LessonValidationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonValidation::class);
    }

    /**
     * Finds a validation record for a specific user and lesson combination.
     *
     * @param Lesson    $lesson The lesson to check
     * @param User|null $user   The user to check
     * @return LessonValidation|null Returns null if not yet validated
     */

    public function findValidationByUserAndLesson(
        Lesson $lesson,
        User $user
    ): ?LessonValidation
    {
        return $this->findOneBy([
            'lesson' => $lesson,
            'user' => $user,
        ]);
    }

    //    /**
    //     * @return LessonValidation[] Returns an array of LessonValidation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?LessonValidation
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
