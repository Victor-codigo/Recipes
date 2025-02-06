<?php

declare(strict_types=1);

namespace App\Form\Factory;

use App\Form\Factory\Form\FormTranslated;
use App\Form\Factory\Form\FormTranslatedInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormFactoryExtended extends FormFactory implements FormFactoryExtendedInterface
{
    private TranslatorInterface $translator;
    private FlashBagInterface $flashBag;

    /**
     * @throws \LogicException
     */
    public function __construct(FormRegistryInterface $registry, TranslatorInterface $translator, RequestStack $request)
    {
        parent::__construct($registry);

        $this->translator = $translator;
        $session = $request->getSession();

        if (!$session instanceof Session) {
            throw new \LogicException('FormFactoryExtended needs to have a session available.');
        }

        $this->flashBag = $session->getFlashBag();
    }

    /**
     * Returns a form, for translated messages.
     *
     * @see createNamedBuilder()
     *
     * @param mixed                $data    The initial data
     * @param array<string, mixed> $options
     *
     * @return FormTranslatedInterface<FormTranslated>
     *
     * @throws InvalidOptionsException if any given option is not applicable to the given type
     */
    public function createNamedTranslated(string $name, string $type, ?string $locale = null, mixed $data = null, array $options = []): FormTranslatedInterface
    {
        $builder = $this->createNamedBuilder($name, $type, $data, $options);

        /* @var FormInterface<FormTranslated> */
        $form = new FormTranslated($builder->getFormConfig(), $this->translator, $this->flashBag, $locale);

        return $form;
    }
}
