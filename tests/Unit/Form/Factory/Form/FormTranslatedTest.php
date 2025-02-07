<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Factory\Form;

use App\Form\Factory\Form\FormTranslated;
use App\Tests\Traits\TestingFormTrait;
use App\Tests\Unit\Form\Fixture\FormTypeForTesting;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
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
    private FlashBagInterface&MockObject $flashBag;
    private CsrfTokenManagerInterface&MockObject $csrfToneManager;
    private ResolvedFormTypeInterface&MockObject $resolvedFormType;
    private FormTypeForTesting $formType;
    private string $locale = 'locale';

    protected function setUp(): void
    {
        parent::setUp();

        $this->formConfig = $this->createMock(FormConfigInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->flashBag = $this->createMock(FlashBagInterface::class);
        $this->resolvedFormType = $this->createMock(ResolvedFormTypeInterface::class);
        $this->csrfToneManager = $this->createMock(CsrfTokenManagerInterface::class);
        $this->formType = new FormTypeForTesting($this->translator, $this->csrfToneManager);
        $this->createStubForGetInnerType($this->formConfig, $this->resolvedFormType, $this->formType);

        $this->object = new FormTranslated(
            $this->formConfig,
            $this->translator,
            $this->flashBag,
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
        $this->createSubFormMethodTrans($errors, $errorsTranslated);

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
                self::equalTo(FormTypeForTesting::TRANSLATION_DOMAIN),
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

    #[Test]
    public function itShouldGetMessagesOkTranslated(): void
    {
        $return = $this->object->getMessagesSuccessTranslated();

        self::assertEquals($this->formType->getFormSuccessMessages(), $return);
    }

    #[Test]
    public function itShouldAddSuccessFormFlashMessages(): void
    {
        $flashBagSuccessType = 'success';
        $flashBagErrorType = 'error';
        $formSuccessMessages = $this->formType->getFormSuccessMessages();

        $flashBagAddInvokeCount = $this->exactly($formSuccessMessages->count());
        $this->flashBag
            ->expects($flashBagAddInvokeCount)
            ->method('add')
            ->with(
                $flashBagSuccessType,
                self::callback(function (mixed $message) use ($formSuccessMessages, $flashBagAddInvokeCount): bool {
                    self::assertEquals($formSuccessMessages->get($flashBagAddInvokeCount->numberOfInvocations() - 1), $message);

                    return true;
                }));

        $this->object->addFlashMessagesTranslated($flashBagSuccessType, $flashBagErrorType, true);
    }

    #[Test]
    public function itShouldAddErrorFormFlashMessages(): void
    {
        $flashBagSuccessType = 'success';
        $flashBagErrorType = 'error';
        $errors = $this->createErrors();
        $errors->map(fn (FormError $error): FormTranslated => $this->object->addError($error));
        $errorsTranslated = $this->getErrorsTranslated($errors, $this->object);

        $flashBagAddInvokeCount = $this->exactly($errors->count());
        $this->flashBag
            ->expects($flashBagAddInvokeCount)
            ->method('add')
            ->with(
                $flashBagErrorType,
                self::callback(function (mixed $message) use ($errorsTranslated, $flashBagAddInvokeCount): bool {
                    self::assertEquals($errorsTranslated->get($flashBagAddInvokeCount->numberOfInvocations() - 1)?->getMessage(), $message);

                    return true;
                }));

        $this->createSubFormMethodTrans($errors, $errorsTranslated);

        $this->object->addFlashMessagesTranslated($flashBagSuccessType, $flashBagErrorType, true);
    }
}
