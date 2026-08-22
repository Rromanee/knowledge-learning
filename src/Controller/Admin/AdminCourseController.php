<?php

namespace App\Controller\Admin;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Manages course CRUD operations in the backoffice.
 */
final class AdminCourseController extends AbstractController
{
    #[Route('/admin/courses', name: 'app_admin_courses')]
    public function index(
        CourseRepository $courseRepository
    ): Response {
        return $this->render('admin/courses.html.twig', [
            'courses' => $courseRepository->findAll(),
        ]);
    }

    #[Route('/admin/courses/new', name: 'app_admin_course_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $course = new Course();

        $form = $this->createForm(
            CourseType::class,
            $course
        );

        $form->handleRequest($request);

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $user = $this->getUser();

            if ($user) {
                $course->setCreatedBy($user->getUserIdentifier());
            }

            $entityManager->persist($course);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Cursus créé avec succès.'
            );

            return $this->redirectToRoute(
                'app_admin_courses'
            );
        }

        return $this->render('admin/course_form.html.twig', [
            'form' => $form,
            'title' => 'Créer un cursus',
        ]);
    }

    #[Route('/admin/courses/{id}/edit', name: 'app_admin_course_edit')]
    public function edit(
        Course $course,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(
            CourseType::class,
            $course
        );

        $form->handleRequest($request);

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $user = $this->getUser();

            $course->setUpdatedAt(new \DateTimeImmutable());

            if ($user) {
                $course->setUpdatedBy($user->getUserIdentifier());
            }

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Cursus modifié avec succès.'
            );

            return $this->redirectToRoute(
                'app_admin_courses'
            );
        }

        return $this->render('admin/course_form.html.twig', [
            'form' => $form,
            'title' => 'Modifier un cursus',
        ]);
    }

    #[Route('/admin/courses/{id}/delete', name: 'app_admin_course_delete')]
    public function delete(
        Course $course,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        if (
            !$this->isCsrfTokenValid(
                'delete_course_' . $course->getId(),
                $request->request->get('_token')
            )
        ) {
            throw $this->createAccessDeniedException(
                'Invalid CSRF token.'
            );
        }

        $entityManager->remove($course);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Cursus supprimé avec succès.'
        );

        return $this->redirectToRoute(
            'app_admin_courses'
        );
    }
}