<?php

namespace App\Controller;

use App\Entity\Certification;
use App\Entity\Lesson;
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

        $course = $lesson->getCourse();

        $totalLessons = count($course->getLessons());

        $validatedLessons = 0;

        foreach ($course->getLessons() as $courseLesson) {
            $validation = $lessonValidationRepository
                ->findValidationByUserAndLesson(
                    $courseLesson,
                    $this->getUser()
                );

            if ($validation) {
                $validatedLessons++;
            }
        }

        if ($validatedLessons === $totalLessons) {
            
            $certification = new Certification();
            $certification->setUser($this->getUser());
            $certification->setTheme($course->getTheme());
            $certification->setObtainedAt(new \DateTimeImmutable());

            $entityManager->persist($certification);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Félicitations ! Certification obtenue pour le thème '.$course->getTheme()->getTitle().'.'
            );
        }

        $this->addFlash('success', 'Leçon validée.');

        return $this->redirectToRoute('app_lesson', [
            'id' => $lesson->getId(),
        ]);
    }
}