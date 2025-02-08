<?php

declare(strict_types=1);

namespace App\Form\Factory\Form;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;

/**
 * @extends FormInterface<FormTranslatedInterface>
 */
interface FormTranslatedInterface extends FormInterface
{
    /**
     * @return FormErrorIterator<FormError>
     */
    public function getErrorsTranslated(bool $deep = false, bool $flatten = true): FormErrorIterator;

    /**
     * @return Collection<int, string>
     */
    public function getMessagesSuccessTranslated(): Collection;

    public function addFlashMessagesTranslated(string $messagesSuccessType, string $messagesErrorType, bool $deep): void;
}
