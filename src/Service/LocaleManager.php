<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class LocaleManager
{
    private string $defaultLocale;
    private array $availableLocales;
    private RequestStack $requestStack;

    public function __construct(
        string $defaultLocale,
        array $locales,
        RequestStack $requestStack
    ) {
        $this->defaultLocale = $defaultLocale;
        $this->availableLocales = $locales;
        $this->requestStack = $requestStack;
    }

    public function getCurrentLocale(): string
    {
        return $this->requestStack->getCurrentRequest()?->getLocale() 
            ?? $this->defaultLocale;
    }

    public function getAvailableLocales(): array
    {
        return $this->availableLocales;
    }

    public function isValidLocale(string $locale): bool
    {
        return in_array($locale, $this->availableLocales);
    }
}