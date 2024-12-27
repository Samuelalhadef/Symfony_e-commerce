<?php
// src/EventSubscriber/LocaleSubscriber.php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private string $defaultLocale;
    private array $supportedLocales;

    public function __construct(string $defaultLocale = 'fr', array $supportedLocales = ['fr', 'en', 'ru'])
    {
        $this->defaultLocale = $defaultLocale;
        $this->supportedLocales = $supportedLocales;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        
        if (!$event->isMainRequest()) {
            return;
        }

        // Récupère la locale depuis l'URL
        $locale = $request->attributes->get('_locale');
        
        // Si pas de locale dans l'URL, essaie de la récupérer depuis la session
        if (!$locale) {
            $locale = $request->getSession()->get('_locale', $this->defaultLocale);
        }
        
        // Vérifie que la locale est supportée
        if (!in_array($locale, $this->supportedLocales)) {
            $locale = $this->defaultLocale;
        }
        
        // Définit la locale pour la requête en cours
        $request->setLocale($locale);
        
        // Sauvegarde la locale en session
        $request->getSession()->set('_locale', $locale);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}