<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class LocaleHelper
{
    private $containerBag;

    public function __construct(
        ContainerBagInterface $containerBag
    ) {
        $this->containerBag = $containerBag;
    }

    public function getDefaultLocale(): string
    {
        return $this->containerBag->get('locale');
    }

    public function getAllLocales(): array
    {
        return $this->containerBag->get('app_locales');
    }

    public function getLocalesAsChoices(): array
    {
        $locales = $this->containerBag->get('app_locales');

        $choices = [];
        foreach ($locales as $langCode => $langName) {
            $choices[$langName] = $langCode;
        }

        return $choices;
    }
}