<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Handles the search functionality of the Knowledge Learning platform.
 */
final class SearchController extends AbstractController
{
    /**
     * Searches for courses, themes and lessons matching the user's query.
     *
     * @param Request $request The current HTTP request.
     * @param CourseRepository $courseRepository Repository used to search courses.
     * @param ThemeRepository $themeRepository Repository used to search themes.
     * @param LessonRepository $lessonRepository Repository used to search lessons.
     *
     * @return Response The search results page.
     */
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function index(
        Request $request,
        CourseRepository $courseRepository,
        ThemeRepository $themeRepository,
        LessonRepository $lessonRepository
    ): Response {
        $query = trim((string) $request->query->get('q', ''));

        $courses = [];
        $themes = [];
        $lessons = [];

        if ($query !== '') {
            $courses = $courseRepository->search($query);
            $themes = $themeRepository->search($query);
            $lessons = $lessonRepository->search($query);
        }

        return $this->render('search/index.html.twig', [
            'query' => $query,
            'courses' => $courses,
            'themes' => $themes,
            'lessons' => $lessons,
        ]);
    }
}
