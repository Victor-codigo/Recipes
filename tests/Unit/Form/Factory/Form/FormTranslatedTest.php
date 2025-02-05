<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Factory\Form;

use App\Form\Factory\Form\FormTranslated;
use App\Tests\Traits\TestingFormTrait;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormError;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormTranslatedTest extends TestCase
{
    use TestingFormTrait;

    private FormTranslated $object;
    /**
     * @var FormConfigInterface<object>&MockObject
     */
    private FormConfigInterface&MockObject $formConfig;
    private TranslatorInterface&MockObject $translator;
    private string $translationDomain = 'translation_domain';
    private string $locale = 'locale';

    protected function setUp(): void
    {
        parent::setUp();

        $this->formConfig = $this->createMock(FormConfigInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->object = new FormTranslated(
            $this->formConfig,
            $this->translator,
            $this->translationDomain,
            $this->locale
        );
    }

    #[Test]
    public function itShouldGetErrorsTranslatedDeepAndFlattenAreTrue(): void
    {
        $deep = true;
        $flatten = true;
        $errors = $this->createErrors();
        $errors->map(fn (FormError $error): FormTranslated => $this->object->addError($error));
        $errorsTranslated = $this->getErrorsTranslated(
            new ArrayCollection(iterator_to_array($this->object->getErrors(true))),
            $this->object
        );

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
                self::equalTo($this->translationDomain),
                self::equalTo($this->locale)
            )
            ->willReturnOnConsecutiveCalls(
                $errorsTranslated->get(0)?->getMessage(),
                $errorsTranslated->get(1)?->getMessage(),
                $errorsTranslated->get(2)?->getMessage(),
                $errorsTranslated->get(3)?->getMessage(),
            );

        $return = $this->object->getErrorsTranslated($deep, $flatten);

        self::assertCount($errorsTranslated->count(), $return);
        $errorTranslated = $errorsTranslated->first();
        foreach ($return as $errorReturned) {
            self::assertEquals($errorTranslated, $errorReturned);

            $errorTranslated = $errorsTranslated->next();
        }
    }

    #[Test]
    public function itShouldGetErrorsTranslatedDeepAndFlattenIAreFalse(): void
    {
        $deep = false;
        $flatten = false;
        $errors = $this->createErrors();
        $errors->map(fn (FormError $error): FormTranslated => $this->object->addError($error));
        $errorsTranslated = $this->getErrorsTranslated(
            new ArrayCollection(iterator_to_array($this->object->getErrors(true))),
            $this->object
        );

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
                self::equalTo($this->translationDomain),
                self::equalTo($this->locale)
            )
            ->willReturnOnConsecutiveCalls(
                $errorsTranslated->get(0)?->getMessage(),
                $errorsTranslated->get(1)?->getMessage(),
                $errorsTranslated->get(2)?->getMessage(),
                $errorsTranslated->get(3)?->getMessage(),
            );

        $return = $this->object->getErrorsTranslated($deep, $flatten);

        self::assertCount($errorsTranslated->count(), $return);
        $errorTranslated = $errorsTranslated->first();
        foreach ($return as $errorReturned) {
            self::assertEquals($errorTranslated, $errorReturned);

            $errorTranslated = $errorsTranslated->next();
        }
    }

    #[Test]
    public function itShouldNoErrorsFormHasNoErrors(): void
    {
        $deep = true;
        $flatten = true;

        $this->translator
            ->expects($this->never())
            ->method('trans');

        $return = $this->object->getErrorsTranslated($deep, $flatten);

        self::assertCount(0, $return);
    }
}
