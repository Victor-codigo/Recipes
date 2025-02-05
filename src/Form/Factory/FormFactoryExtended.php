<?php

declare(strict_types=1);

namespace App\Form\Factory;

use App\Form\Factory\Form\FormTranslated;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormFactoryExtended extends FormFactory implements FormFactoryExtendedInterface
{
    private TranslatorInterface $translator;

    public function __construct(FormRegistryInterface $registry, TranslatorInterface $translator)
    {
        parent::__construct($registry);

        $this->translator = $translator;
    }

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
    public function createNamedTranslated(string $name, string $type, string $translationDomain, ?string $locale = null, mixed $data = null, array $options = []): FormInterface
    {
        $builder = $this->createNamedBuilder($name, $type, $data, $options);

        /* @var FormInterface<FormTranslated> */
        $form = new FormTranslated($builder->getFormConfig(), $this->translator, $translationDomain, $locale);

        return $form;
    }
}
