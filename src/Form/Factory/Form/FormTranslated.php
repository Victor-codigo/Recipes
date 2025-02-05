<?php

declare(strict_types=1);

namespace App\Form\Factory\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormTranslated extends Form
{
    private TranslatorInterface $translator;
    public readonly string $translationDomain;
    public readonly ?string $locale;

    /**
     * @template TData
     *
     * @param FormConfigInterface<TData> $config
     */
    public function __construct(FormConfigInterface $config, TranslatorInterface $translator, string $translationDomain, ?string $locale)
    {
        parent::__construct($config);

        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->locale = $locale;
    }

    /**
     * @return FormErrorIterator<FormError>
     */
    public function getErrorsTranslated(bool $deep = false, bool $flatten = true): FormErrorIterator
    {
        $errors = $this->getErrors($deep, $flatten);

        $errorsTranslated = [];
        foreach ($errors as $error) {
            $errorTranslated = new FormError(
                $this->translator->trans($error->getMessage(), $error->getMessageParameters(), $this->translationDomain, $this->locale),
                $error->getMessageTemplate(),
                $error->getMessageParameters(),
                $error->getMessagePluralization(),
                $error->getCause()
            );

            $errorTranslated->setOrigin($this);
            $errorsTranslated[] = $errorTranslated;
        }

        return new FormErrorIterator($this, $errorsTranslated);
    }
}
