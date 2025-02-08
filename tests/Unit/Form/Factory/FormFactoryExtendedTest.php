<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Factory;

use App\Form\Factory\FormFactoryExtended;
use App\Form\Factory\Form\FormTranslated;
use App\Tests\Traits\TestingFormTrait;
use App\Tests\Unit\Form\Fixture\FormTypeForTesting;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormFactoryExtendedTest extends TestCase
{
    use TestingFormTrait;

    /**
     * @var FormFactoryExtended<mixed>
     */
    private FormFactoryExtended $object;
    private FormFactoryInterface&MockObject $formFactory;
    private FormRegistryInterface&MockObject $formRegistry;
    /**
     * @var FormInterface<mixed>&MockObject
     */
    private FormInterface&MockObject $form;
    /**
     * @var FormConfigInterface<object>&MockObject
     */
    private FormConfigInterface&MockObject $formConfig;
    private TranslatorInterface&MockObject $translator;
    private RequestStack&MockObject $request;
    private FlashBagInterface&MockObject $flashBag;
    private ResolvedFormTypeInterface&MockObject $resolvedFormType;
    /**
     * @var FormBuilderInterface<object>&MockObject
     */
    private FormBuilderInterface&MockObject $formBuilder;
    private CsrfTokenManagerInterface&MockObject $csrfToneManager;
    private Session&MockObject $session;
    private FormTypeForTesting $formType;
    private string $locale = 'locale';

    protected function setUp(): void
    {
        parent::setUp();

        $this->formFactory = $this->createMock(FormFactoryInterface::class);
        $this->formRegistry = $this->createMock(FormRegistryInterface::class);
        $this->form = $this->createMock(FormInterface::class);
        $this->formConfig = $this->createMock(FormConfigInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->request = $this->createMock(RequestStack::class);
        $this->flashBag = $this->createMock(FlashBagInterface::class);
        $this->resolvedFormType = $this->createMock(ResolvedFormTypeInterface::class);
        $this->session = $this->createMock(Session::class);
        $this->formBuilder = $this->createMock(FormBuilderInterface::class);
        $this->csrfToneManager = $this->createMock(CsrfTokenManagerInterface::class);

        $this->formType = new FormTypeForTesting($this->translator, $this->csrfToneManager);

        $this->createStubForGetInnerType($this->formConfig, $this->resolvedFormType, $this->formType);

        $this->formFactory
            ->expects($this->once())
            ->method('createNamedBuilder')
            ->willReturn($this->formBuilder);

        $this->form
            ->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->formConfig);

        $this->formRegistry
            ->expects($this->any())
            ->method('getType')
            ->with(FormTypeForTesting::class)
            ->willReturn($this->resolvedFormType);

        $this->formBuilder
            ->expects($this->once())
            ->method('getForm')
            ->willReturn($this->form);

        $this->request
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->session);

        $this->session
            ->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($this->flashBag);

        $this->object = new FormFactoryExtended(
            $this->formFactory,
            $this->translator,
            $this->request
        );
    }

    #[Test]
    public function itShouldCreateAFormTranslated(): void
    {
        $formName = 'formName';
        $formType = FormTypeForTesting::class;
        $formExpected = new FormTranslated($this->form, $this->translator, $this->flashBag, $this->locale);

        $return = $this->object->createNamedTranslated($formName, $formType, $this->locale);

        self::assertInstanceOf(FormTranslated::class, $return);
        self::assertEquals($formExpected->getName(), $return->getName());
        self::assertEquals($formExpected->translationDomain, $return->translationDomain);
        self::assertEquals($formExpected->locale, $return->locale);
    }
}
