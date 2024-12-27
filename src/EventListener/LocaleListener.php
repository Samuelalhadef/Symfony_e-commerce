<?php
// src/EventListener/LocaleListener.php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        
        // Vérifie si une locale est définie dans la session
        if ($locale = $request->getSession()->get('_locale')) {
            $request->setLocale($locale);
        }
        
        // Si une locale est définie dans l'URL, la sauvegarde en session
        if ($request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $request->attributes->get('_locale'));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}