<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Recipe\RecipeCreate;

use App\Common\Image\Exception\ImageResizeException;
use App\Common\Image\ImageInterface;
use App\Entity\Recipe;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormDataMapper;
use App\Repository\RecipeRepository;
use App\Service\Exception\RecipeCreateException;
use App\Service\Recipe\RecipeCreate\RecipeCreateService;
use App\Tests\Traits\TestingRecipeTrait;
use App\Tests\Traits\TestingUserTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use VictorCodigo\SymfonyFormExtended\Form\FormExtendedInterface;

class RecipeCreateServiceTest extends TestCase
{
    use TestingRecipeTrait;
    use TestingUserTrait;

    private const string RECIPE_UPLOAD_PATH = 'public/images/upload/recipe';
    private const int RECIPE_IMAGE_SAVE_WIDTH = 300;
    private const int RECIPE_IMAGE_SAVE_HEIGHT = 300;

    private RecipeCreateService $object;
    private RecipeRepository&MockObject $recipeRepository;
    private Security&MockObject $security;
    private RecipeCreateFormDataMapper&MockObject $recipeCreateFormDataMapper;
    private Request&MockObject $request;
    private FormExtendedInterface&MockObject $form;
    private ImageInterface&MockObject $image;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = $this->createMock(Request::class);
        $this->form = $this->createMock(FormExtendedInterface::class);
        $this->recipeRepository = $this->createMock(RecipeRepository::class);
        $this->recipeCreateFormDataMapper = $this->createMock(RecipeCreateFormDataMapper::class);
        $this->security = $this->createMock(Security::class);
        $this->image = $this->createMock(ImageInterface::class);
        $this->object = new RecipeCreateService(
            $this->recipeRepository,
            $this->security,
            $this->recipeCreateFormDataMapper,
            $this->image,
            self::RECIPE_UPLOAD_PATH,
            self::RECIPE_IMAGE_SAVE_WIDTH,
            self::RECIPE_IMAGE_SAVE_HEIGHT
        );
    }

    #[Test]
    public function itShouldCreateARecipe(): void
    {
        $user = $this->getUsersFixtures()->first();
        /** @var Recipe */
        $recipe = $this->getRecipesFixtures()->first();
        $recipe->setImage('image.jpg');
        $recipeCreateFormDataValidation = $this->createRecipeFormDataValidation();
        $groupId = 'recipe group id';
        $recipeId = 'recipe id';

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($recipeCreateFormDataValidation);

        $this->form
            ->expects($this->once())
            ->method('uploadFiles')
            ->with($this->request, self::RECIPE_UPLOAD_PATH);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->recipeRepository
            ->expects($this->once())
            ->method('uuidCreate')
            ->willReturn($recipeId);

        $this->recipeCreateFormDataMapper
            ->expects($this->once())
            ->method('toEntity')
            ->with($recipeCreateFormDataValidation, $user, $recipeId, $groupId)
            ->willReturn($recipe);

        $this->image
            ->expects($this->once())
            ->method('resizeToAFrame')
            ->with(
                self::RECIPE_UPLOAD_PATH.'/'.$recipe->getImage(),
                self::RECIPE_IMAGE_SAVE_WIDTH,
                self::RECIPE_IMAGE_SAVE_HEIGHT
            );

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe);

        $this->object->__invoke($this->request, $this->form, $groupId);
    }

    #[Test]
    public function itShouldCreateARecipeNoImage(): void
    {
        $user = $this->getUsersFixtures()->first();
        /** @var Recipe */
        $recipe = $this->getRecipesFixtures()->first();
        $recipeCreateFormDataValidation = $this->createRecipeFormDataValidation();
        $groupId = 'recipe group id';
        $recipeId = 'recipe id';

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($recipeCreateFormDataValidation);

        $this->form
            ->expects($this->once())
            ->method('uploadFiles')
            ->with($this->request, self::RECIPE_UPLOAD_PATH);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->recipeRepository
            ->expects($this->once())
            ->method('uuidCreate')
            ->willReturn($recipeId);

        $this->recipeCreateFormDataMapper
            ->expects($this->once())
            ->method('toEntity')
            ->with($recipeCreateFormDataValidation, $user, $recipeId, $groupId)
            ->willReturn($recipe);

        $this->image
            ->expects($this->never())
            ->method('resizeToAFrame');

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe);

        $this->object->__invoke($this->request, $this->form, $groupId);
    }

    #[Test]
    public function itShouldFailCreatingARecipeErrorResizingImage(): void
    {
        $user = $this->getUsersFixtures()->first();
        /** @var Recipe */
        $recipe = $this->getRecipesFixtures()->first();
        $recipe->setImage('image.jpg');
        $recipeCreateFormDataValidation = $this->createRecipeFormDataValidation();
        $groupId = 'recipe group id';
        $recipeId = 'recipe id';

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($recipeCreateFormDataValidation);

        $this->form
            ->expects($this->once())
            ->method('uploadFiles')
            ->with($this->request, self::RECIPE_UPLOAD_PATH);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->recipeRepository
            ->expects($this->once())
            ->method('uuidCreate')
            ->willReturn($recipeId);

        $this->recipeCreateFormDataMapper
            ->expects($this->once())
            ->method('toEntity')
            ->with($recipeCreateFormDataValidation, $user, $recipeId, $groupId)
            ->willReturn($recipe);

        $this->image
            ->expects($this->once())
            ->method('resizeToAFrame')
            ->with(
                self::RECIPE_UPLOAD_PATH.'/'.$recipe->getImage(),
                self::RECIPE_IMAGE_SAVE_WIDTH,
                self::RECIPE_IMAGE_SAVE_HEIGHT
            )
            ->willThrowException(new ImageResizeException('Error resizing image'));

        $this->recipeRepository
            ->expects($this->never())
            ->method('save');

        $this->expectException(RecipeCreateException::class);
        $this->expectExceptionMessage('Error resizing image');
        $this->object->__invoke($this->request, $this->form, $groupId);
    }

    #[Test]
    public function itShouldFailCreatingARecipeErrorSaving(): void
    {
        $user = $this->getUsersFixtures()->first();
        /** @var Recipe */
        $recipe = $this->getRecipesFixtures()->first();
        $recipe->setImage('image.jpg');
        $recipeCreateFormDataValidation = $this->createRecipeFormDataValidation();
        $groupId = 'recipe group id';
        $recipeId = 'recipe id';

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($recipeCreateFormDataValidation);

        $this->form
            ->expects($this->once())
            ->method('uploadFiles')
            ->with($this->request, self::RECIPE_UPLOAD_PATH);

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->recipeRepository
            ->expects($this->once())
            ->method('uuidCreate')
            ->willReturn($recipeId);

        $this->recipeCreateFormDataMapper
            ->expects($this->once())
            ->method('toEntity')
            ->with($recipeCreateFormDataValidation, $user, $recipeId, $groupId)
            ->willReturn($recipe);

        $this->image
            ->expects($this->once())
            ->method('resizeToAFrame')
            ->with(
                self::RECIPE_UPLOAD_PATH.'/'.$recipe->getImage(),
                self::RECIPE_IMAGE_SAVE_WIDTH,
                self::RECIPE_IMAGE_SAVE_HEIGHT
            );

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe)
            ->willThrowException(new \Exception('Error saving recipe'));

        $this->expectException(\Exception::class);
        $this->object->__invoke($this->request, $this->form, $groupId);
    }
}
