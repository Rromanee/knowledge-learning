<?php

namespace App\Controller;

use App\Entity\Certification;
use App\Entity\Lesson;
use App\Entity\User;
use App\Entity\LessonValidation;
use App\Repository\LessonValidationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LessonValidationController extends AbstractController
{
    #[Route('/lesson/{id}/validate', name: 'app_lesson_validation')]
    public function index(
        Lesson $lesson,
        LessonValidationRepository $lessonValidationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $existingValidation = $lessonValidationRepository
            ->findValidationByUserAndLesson(
                $lesson,
                $this->getUser()
            );

        if ($existingValidation) {
            $this->addFlash('warning', 'Cette leçon est déjà validée.');

            return $this->redirectToRoute('app_lesson', [
                'id' => $lesson->getId(),
            ]);
        }

        $lessonValidation = new LessonValidation();
        $lessonValidation->setUser($this->getUser());
        $lessonValidation->setLesson($lesson);
        $lessonValidation->setValidatedAt(new \DateTimeImmutable());

        $entityManager->persist($lessonValidation);
        $entityManager->flush();

        $theme = $lesson->getCourse()->getTheme();

        $totalLessons = 0;
        $validatedLessons = 0;

        foreach ($theme->getCourses() as $course) {
            foreach ($course->getLessons() as $courseLesson) {

                $totalLessons++;

                $validation = $lessonValidationRepository
                    ->findValidationByUserAndLesson(
                        $courseLesson,
                        $this->getUser()
                    );

                if ($validation) {
                    $validatedLessons++;
                }
            }
        }

        /** @var User $user */
        $user = $this->getUser();

        $existingCertification = $user
            ->getCertifications()
            ->filter(
                fn ($certification) =>
                    $certification->getTheme() === $theme
            )
            ->first();

        if ($validatedLessons === $totalLessons && !$existingCertification) {

            $certification = new Certification();
            $certification->setUser($this->getUser());
            $certification->setTheme($theme);
            $certification->setObtainedAt(new \DateTimeImmutable());

            $entityManager->persist($certification);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Félicitations ! Certification obtenue pour le thème ' . $theme->getTitle() . '.'
            );
        }

        $this->addFlash('success', 'Leçon validée.');

        return $this->redirectToRoute('app_lesson', [
            'id' => $lesson->getId(),
        ]);
    }
}