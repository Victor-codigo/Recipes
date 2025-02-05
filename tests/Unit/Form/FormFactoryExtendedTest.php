<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Form\Factory\FormFactoryExtended;
use App\Form\Factory\Form\FormTranslated;
use App\Tests\Unit\Form\Fixture\FormTypeForTesting;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormFactoryExtendedTest extends TestCase
{
    private FormFactoryExtended $object;
    private FormRegistryInterface&MockObject $formRegistry;
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

        $this->formRegistry = $this->createMock(FormRegistryInterface::class);
        $this->formConfig = $this->createMock(FormConfigInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->object = new FormFactoryExtended(
            $this->formRegistry,
            $this->translator
        );
    }

    #[Test]
    public function itShouldCreateAFormTranslated(): void
    {
        $formName = 'formName';
        $formType = FormTypeForTesting::class;
        $formExpected = new FormTranslated($this->formConfig, $this->translator, $this->translationDomain, $this->locale);

        $return = $this->object->createNamedTranslated($formName, $formType, $this->translationDomain, $this->locale);

        self::assertInstanceOf(FormTranslated::class, $return);
        self::assertEquals($formExpected->getName(), $return->getName());
        self::assertEquals($formExpected->translationDomain, $return->translationDomain);
        self::assertEquals($formExpected->locale, $return->locale);
    }
}
