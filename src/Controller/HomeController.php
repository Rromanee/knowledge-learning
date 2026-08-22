<?php

namespace App\Controller;

use App\Repository\CourseRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Displays the home page with all available themes
 * and allows users to search for courses.
 */
final class HomeController extends AbstractController
{
    /**
     * Displays the home page.
     *
     * When a search term is provided, matching courses are retrieved
     * from the CourseRepository.
     *
     * @param ThemeRepository $themeRepository Repository used to retrieve themes.
     * @param CourseRepository $courseRepository Repository used to search courses.
     * @param Request $request HTTP request containing the search term.
     *
     * @return Response The rendered home page.
     */
    #[Route('/', name: 'app_home')]
    public function index(
        ThemeRepository $themeRepository,
        CourseRepository $courseRepository,
        Request $request
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $search = trim($request->query->get('search', ''));

        $courses = [];

        if ($search !== '') {
            $courses = $courseRepository->search($search);
        }

        return $this->render('home/index.html.twig', [
            'themes' => $themeRepository->findAll(),
            'courses' => $courses,
            'search' => $search,
        ]);
    }
}
