<?php
// src/Controller/LocaleController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    #[Route('/change-locale/{locale}', name: 'change_locale')]
    public function changeLocale(Request $request, string $locale): Response
    {
        // Stocke la langue choisie en session
        $request->getSession()->set('_locale', $locale);
        
        // Récupère l'URL précédente
        $referer = $request->headers->get('referer');
        
        // Si pas de référent, redirige vers la page d'accueil
        if ($referer === null) {
            return $this->redirectToRoute('app_home', ['_locale' => $locale]);
        }
        
        // Remplace l'ancienne locale dans l'URL par la nouvelle
        $newUrl = preg_replace(
            '~/([a-z]{2})(/|$)~',
            '/' . $locale . '$2',
            $referer
        );
        
        return $this->redirect($newUrl);
    }
}