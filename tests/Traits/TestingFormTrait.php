<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Form\Factory\Form\FormTranslated;
use App\Tests\Unit\Form\Fixture\FormTypeForTesting;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

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

    /**
     * @param FormConfigInterface<object>&MockObject $formConfig
     */
    private function createStubForGetInnerType(FormConfigInterface&MockObject $formConfig, ResolvedFormTypeInterface&MockObject $resolvedFormType, FormTypeForTesting $formType): void
    {
        $formConfig
            ->expects($this->any())
            ->method('getType')
            ->willReturn($resolvedFormType);

        $resolvedFormType
            ->expects($this->any())
            ->method('getInnerType')
            ->willReturn($formType);
    }

    /**
     * @param Collection<int, FormError> $errors
     * @param Collection<int, FormError> $errorsTranslated
     */
    private function createSubFormMethodTrans(Collection $errors, Collection $errorsTranslated): void
    {
        $translationDomain = $this->exactly($errors->count());
        $this->translator
            ->expects($translationDomain)
            ->method('trans')
            ->with(
                self::callback(function (string $message) use ($translationDomain, $errors): bool {
                    self::assertEquals($errors->get($translationDomain->numberOfInvocations() - 1)?->getMessage(), $message);

                    return true;
                }),
                self::callback(function (array $params) use ($translationDomain, $errors): bool {
                    self::assertEquals($errors->get($translationDomain->numberOfInvocations() - 1)?->getMessageParameters(), $params);

                    return true;
                }),
                self::equalTo(FormTypeForTesting::TRANSLATION_DOMAIN),
                self::equalTo($this->locale)
            )
            ->willReturnCallback(fn (): ?string => $errorsTranslated->get($translationDomain->numberOfInvocations() - 1)?->getMessage());
    }
}
