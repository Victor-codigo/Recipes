<?php

declare(strict_types=1);

namespace App\Form\Factory;

use App\Form\Factory\Form\FormTranslated;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

interface FormFactoryExtendedInterface extends FormFactoryInterface
{
    /**
     * Returns a form, for translated messages.
     *
     * @see createNamedBuilder()
     *
     * @param mixed                $data    The initial data
     * @param array<string, mixed> $options
     *
     * @return FormInterface<FormTranslated>
     *
     * @throws InvalidOptionsException if any given option is not applicable to the given type
     */
    public function createNamedTranslated(string $name, string $type, string $translationDomain, ?string $locale = null, mixed $data = null, array $options = []): FormInterface;
}
