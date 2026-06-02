<?php

namespace App\Controller;

use App\Entity\Course;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CourseController extends AbstractController
{
    #[Route('/course/{id}', name: 'app_course')]
    public function show(Course $course): Response
    {
        return $this->render('course/index.html.twig', [
            'course' => $course,
        ]);
    }
}