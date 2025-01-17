<?php

declare(strict_types=1);

namespace App\Templates\Components;

use Symfony\Contracts\Translation\TranslatorInterface;

abstract class TwigComponent
{
    abstract protected static function getComponentName(): string;

    private string $translationDomainName;
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->setTranslationDomainName($this->getComponentName());
    }

    protected function translate(string $id, array $params = []): string
    {
        return $this->translator->trans($id, $params, $this->translationDomainName);
    }

    protected function setTranslationDomainName(string $domainName): void
    {
        $this->translationDomainName = $domainName;
    }
}
