<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Form\Factory\Form\FormTranslated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

trait TestingFormTrait
{
    /**
     * @return Collection<int, FormError>
     */
    private function createErrors(): Collection
    {
        return new ArrayCollection([
            new FormError(
                'message.error.msg1',
                'messageTemplate.error.msg1',
                [
                    'param1' => 'value1',
                    'param2' => 'value2',
                ],
                null,
                null
            ),
            new FormError(
                'message.error.msg2',
                'messageTemplate.error.msg2',
                [
                    'param1' => 'value1',
                    'param2' => 'value2',
                ],
                1,
                'cause msg 2'
            ),
            new FormError(
                'message.error.msg3',
                'messageTemplate.error.msg3',
                [
                    'param1' => 'value1',
                ],
                2,
                'cause msg 3'
            ),
        ]);
    }

    /**
     * Adds to error message the string ".translated".
     *
     * @param Collection<int, FormError>    $errors
     * @param FormInterface<FormTranslated> $form
     *
     * @return Collection<int, FormError>
     */
    private function getErrorsTranslated(Collection $errors, FormInterface $form): Collection
    {
        return $errors->map(function (FormError $error) use ($form): FormError {
            $error = new FormError(
                $error->getMessage().'.translated',
                $error->getMessageTemplate(),
                $error->getMessageParameters(),
                $error->getMessagePluralization(),
                $error->getCause()
            );
            $error->setOrigin($form);

            return $error;
        });
    }
}
