<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/** Displays the list of certifications obtained by the current user. */
final class CertificationController extends AbstractController
{
    #[Route('/certifications', name: 'app_certifications')]
    public function show(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        return $this->render('certification/index.html.twig', [
            'certifications' => $user->getCertifications(),
        ]);
    }
}