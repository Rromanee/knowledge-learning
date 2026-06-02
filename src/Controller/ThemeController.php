<?php

namespace App\Controller;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ThemeController extends AbstractController
{
    #[Route('/theme/{id}', name: 'app_theme')]
    public function show(Theme $theme): Response
    {
        return $this->render('theme/index.html.twig', [
            'theme' => $theme,
        ]);
    }
}