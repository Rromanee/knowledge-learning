<?php

namespace App\Controller;

use App\Entity\Certification;
use App\Entity\Lesson;
use App\Entity\LessonValidation;
use App\Entity\User;
use App\Repository\LessonValidationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles lesson validation and automatic certification attribution.
 *
 * When all lessons of a theme are validated, a certification is automatically created.
 */
final class LessonValidationController extends AbstractController
{
    /**
     * Validates a lesson for the current user.
     *
     * If all lessons of the theme are validated,
     * a certification is automatically created.
     */
    #[Route('/lesson/{id}/validate', name: 'app_lesson_validation', methods: ['POST'])]
    public function index(
        Lesson $lesson,
        LessonValidationRepository $lessonValidationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        /*
         * Check whether this lesson has already been validated.
         */
        $existingValidation = $lessonValidationRepository
            ->findValidationByUserAndLesson(
                $lesson,
                $user
            );

        if ($existingValidation) {
            $this->addFlash(
                'warning',
                'Cette leçon est déjà validée.'
            );

            return $this->redirectToRoute('app_lesson', [
                'id' => $lesson->getId(),
            ]);
        }

        /*
         * Create the lesson validation.
         */
        $lessonValidation = new LessonValidation();

        $lessonValidation->setUser($user);
        $lessonValidation->setLesson($lesson);
        $lessonValidation->setValidatedAt(new \DateTimeImmutable());

        $entityManager->persist($lessonValidation);
        $entityManager->flush();

        /*
         * Get the theme associated with the lesson.
         */
        $course = $lesson->getCourse();

        if (!$course || !$course->getTheme()) {
            $this->addFlash(
                'success',
                'Leçon validée.'
            );

            return $this->redirectToRoute('app_lesson', [
                'id' => $lesson->getId(),
            ]);
        }

        $theme = $course->getTheme();

        /*
         * Count all lessons of the theme
         * and the lessons already validated by the user.
         */
        $totalLessons = 0;
        $validatedLessons = 0;

        foreach ($theme->getCourses() as $themeCourse) {
            foreach ($themeCourse->getLessons() as $themeLesson) {
                $totalLessons++;

                $validation = $lessonValidationRepository
                    ->findValidationByUserAndLesson(
                        $themeLesson,
                        $user
                    );

                if ($validation) {
                    $validatedLessons++;
                }
            }
        }

        /*
         * Check whether the user already has
         * a certification for this theme.
         */
        $existingCertification = $user
            ->getCertifications()
            ->filter(
                fn (Certification $certification) =>
                    $certification->getTheme() === $theme
            )
            ->first();

        /*
         * If all lessons of the theme are validated,
         * automatically create the certification.
         */
        if (
            $totalLessons > 0
            && $validatedLessons === $totalLessons
            && !$existingCertification
        ) {
            $certification = new Certification();

            $certification->setUser($user);
            $certification->setTheme($theme);
            $certification->setObtainedAt(new \DateTimeImmutable());

            $entityManager->persist($certification);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Félicitations ! Vous avez obtenu la certification du thème « '
                . $theme->getTitle()
                . ' ».'
            );
        } else {
            $this->addFlash(
                'success',
                'Leçon validée.'
            );
        }

        return $this->redirectToRoute('app_lesson', [
            'id' => $lesson->getId(),
        ]);
    }
}