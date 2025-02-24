<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Recipe\RecipeCreate;

use App\Common\RECIPE_TYPE;
use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormDataValidation;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormType;
use App\Tests\Traits\TestingUserTrait;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormExtensionInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;
use VictorCodigo\SymfonyFormExtended\Factory\FormFactoryExtended;
use VictorCodigo\SymfonyFormExtended\Factory\FormFactoryExtendedInterface;
use VictorCodigo\UploadFile\Adapter\UploadFileService;

class RecipeCreateFormTypeTest extends TypeTestCase
{
    use TestingUserTrait;

    private RecipeCreateFormType $object;
    private FormFactoryExtendedInterface $formFactoryExtended;
    private TranslatorInterface&MockObject $translator;
    private UploadFileService&MockObject $uploadFile;
    private CsrfTokenManagerInterface&MockObject $csrfTokenManager;
    private RequestStack&MockObject $requestStack;
    private Session&MockObject $userSession;
    private FlashBagInterface&MockObject $flashBag;

    protected function setUp(): void
    {
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->uploadFile = $this->createMock(UploadFileService::class);
        $this->csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->userSession = $this->createMock(Session::class);
        $this->flashBag = $this->createMock(FlashBagInterface::class);

        $this->requestStack
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($this->userSession);

        $this->userSession
            ->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($this->flashBag);

        parent::setUp();

        $this->object = new RecipeCreateFormType($this->translator, $this->csrfTokenManager);
        $this->formFactoryExtended = new FormFactoryExtended($this->factory, $this->translator, $this->uploadFile, $this->requestStack);
    }

    /**
     * @return array<int, FormExtensionInterface>
     */
    protected function getExtensions(): array
    {
        $recipeCreateFormType = new RecipeCreateFormType($this->translator, $this->csrfTokenManager);

        $validator = Validation::createValidator();

        // or if you also need to read constraints from attributes
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        return [
            new PreloadedExtension([$recipeCreateFormType], []),
            new ValidatorExtension($validator),
        ];
    }

    /**
     * @param array{
     *  name?: string,
     *  description?: string|null,
     *  image?: UploadedFile|null,
     *  preparation_time?: string|null,
     *  category?: string,
     *  public?: string,
     *  steps?: array<int, string>,
     *  ingredients?: array<int, string>
     * } $formDataSubmitted
     * @param array{
     *      name: string,
     *      description: string|null,
     *      image: UploadedFile|null,
     *      preparation_time: DateTimeImmutable|null,
     *      category: RECIPE_TYPE,
     *      public: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     *  } $formDataExpected
     */
    #[Test]
    #[DataProviderExternal(RecipeCreateFormDataProvider::class, 'formDataDataProvider')]
    public function itShouldValidateAllFieldsFilled(array $formDataSubmitted, array $formDataExpected, bool $expectedIsValid): void
    {
        $formData = new RecipeCreateFormDataValidation();
        $form = $this->formFactoryExtended->createNamedExtended(RECIPE_CREATE_FORM_FIELDS::FORM_NAME->value, RecipeCreateFormType::class, null, $formData);
        $form->submit($formDataSubmitted);

        self::assertEquals($expectedIsValid, $form->isValid());
        self::assertTrue($form->isSynchronized());
        self::assertEquals($formDataExpected, (array) $formData);
    }
}
