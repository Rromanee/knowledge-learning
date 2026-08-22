<?php

namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\LessonValidation;
use App\Entity\User;
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
     * Finds a validation record for a specific user and lesson.
     */
    public function findValidationByUserAndLesson(
        Lesson $lesson,
        User $user
    ): ?LessonValidation {
        return $this->findOneBy([
            'lesson' => $lesson,
            'user' => $user,
        ]);
    }

    /**
     * Returns the number of lessons validated by a user.
     */
    public function countValidatedLessonsByUser(User $user): int
    {
        return (int) $this->createQueryBuilder('lv')
            ->select('COUNT(lv.id)')
            ->andWhere('lv.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}