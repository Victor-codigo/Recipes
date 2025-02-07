<?php

declare(strict_types=1);

namespace App\Form\Factory\Form;

use App\Form\FormTypeBase;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @implements FormTranslatedInterface<FormTranslated>
 */
class FormTranslated extends Form implements FormTranslatedInterface
{
    private TranslatorInterface $translator;
    private FlashBagInterface $flashBag;
    public readonly string $translationDomain;
    public readonly ?string $locale;

    /**
     * @template TData
     *
     * @param FormConfigInterface<TData> $config
     */
    public function __construct(FormConfigInterface $config, TranslatorInterface $translator, FlashBagInterface $flashBag, ?string $locale)
    {
        parent::__construct($config);

        $this->translator = $translator;
        $this->translationDomain = $this->getTranslationDomain();
        $this->flashBag = $flashBag;
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

    /**
     * @return Collection<int, string>
     */
    public function getMessagesSuccessTranslated(): Collection
    {
        /** @var FormTypeTranslatedInterface<FormTypeBase<object>> */
        $formType = $this->getConfig()->getType()->getInnerType();

        return $formType->getFormSuccessMessages();
    }

    public function addFlashMessagesTranslated(string $messagesSuccessType, string $messagesErrorType, bool $deep): void
    {
        $errors = $this->getErrorsTranslated($deep);

        if (0 === $errors->count()) {
            $this->getMessagesSuccessTranslated()
                 ->map(fn (string $message) => $this->flashBag->add($messagesSuccessType, $message));

            return;
        }

        foreach ($errors as $error) {
            $errorMessage = $error->getMessage();
            $this->flashBag->add($messagesErrorType, $errorMessage);
        }
    }

    /**
     * @throws \LogicException
     */
    private function getTranslationDomain(): string
    {
        $formType = $this->getConfig()->getType()->getInnerType();

        if (!$formType instanceof FormTypeTranslatedInterface) {
            throw new \LogicException('Form type must implement FormTypeTranslatedInterface');
        }

        return $formType::TRANSLATION_DOMAIN;
    }
}
