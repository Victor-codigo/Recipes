<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Recipe\RecipeModify;

use App\Common\RECIPE_TYPE;
use App\Entity\Recipe;
use App\Entity\User;
use App\Form\Recipe\RecipeModify\RecipeModifyFormDataMapper;
use App\Form\Recipe\RecipeModify\RecipeModifyFormDataValidation;
use App\Repository\Exception\DBNotFoundException;
use App\Repository\RecipeRepository;
use App\Service\Exception\RecipeModifyException;
use App\Service\Recipe\RecipeModify\RecipeModifyService;
use App\Tests\Traits\TestingAliceBundleTrait;
use App\Tests\Traits\TestingFixturesTrait;
use App\Tests\Traits\TestingImageTrait;
use App\Tests\Traits\TestingRecipeTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\Request;
use VictorCodigo\SymfonyFormExtended\Form\FormExtendedInterface;

class RecipeModifyServiceTest extends TypeTestCase
{
    use TestingAliceBundleTrait;
    use TestingRecipeTrait;
    use TestingFixturesTrait;
    use TestingImageTrait;

    private const string UPLOAD_RECIPES_PATH = 'public/images/upload/recipe';

    private RecipeModifyService $object;
    private RecipeRepository&MockObject $recipeRepository;
    private Filesystem&MockObject $filesystem;
    private Request&MockObject $request;
    private FormExtendedInterface&MockObject $formExtended;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = $this->createMock(Request::class);
        $this->formExtended = $this->createMock(FormExtendedInterface::class);
        $this->recipeRepository = $this->createMock(RecipeRepository::class);
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->object = new RecipeModifyService(
            $this->recipeRepository,
            new RecipeModifyFormDataMapper(),
            $this->filesystem,
            self::UPLOAD_RECIPES_PATH
        );
    }

    protected function createRecipeFormDataValidationWithId(string $id): RecipeModifyFormDataValidation
    {
        $recipeFormValidationData = new RecipeModifyFormDataValidation();
        $recipeFormValidationData->id = $id;
        $recipeFormValidationData->name = 'Recipe name';
        $recipeFormValidationData->description = 'Recipe description';
        $recipeFormValidationData->ingredients = [
            '2 potatoes',
            '1 cucumber',
            '1 onion',
            '1 carrot',
        ];
        $recipeFormValidationData->steps = [
            '1. Prepare the potatoes: Cut the potatoes into bite-sized cubes.',
            '2. Add the cucumbers, onion, and carrot to a large pot.',
            '3. Cook the vegetables according to package instructions.',
            '4. Add the potatoes to the cooked vegetables and cook for 30 minutes.',
        ];
        $recipeFormValidationData->image = null;
        $recipeFormValidationData->image_remove = false;
        $recipeFormValidationData->preparation_time = new \DateTimeImmutable('2025-02-01 12:00:00');
        $recipeFormValidationData->category = RECIPE_TYPE::BREAKFAST;
        $recipeFormValidationData->public = false;

        return $recipeFormValidationData;
    }

    private function createRecipeFromRecipeModifyFormDataValidation(RecipeModifyFormDataValidation $formData, User $user, ?string $groupId): Recipe
    {
        return new Recipe(
            $formData->id,
            $user,
            $groupId,
            $formData->name,
            $formData->category->value,
            $formData->description,
            $formData->preparation_time,
            $formData->ingredients,
            $formData->steps,
            $formData->image?->getFilename(),
            null,
            $formData->public,
        );
    }

    #[Test]
    public function itShouldModifyASavedRecipeAndSaveIt(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('recipe id');
        $recipe = $this->getRecipesFixtures()->first();

        $this->formExtended
            ->expects($this->exactly(2))
            ->method('getData')
            ->willReturn($formData);

        $this->formExtended
            ->expects($this->once())
            ->method('uploadFiles')
            ->with($this->request, self::UPLOAD_RECIPES_PATH, []);

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipeByIdAndGroupIdOrFail')
            ->with($formData->id, null)
            ->willReturn($recipe);

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe);

        $this->object->__invoke($this->request, $this->formExtended, null);
    }

    #[Test]
    public function itShouldModifyASavedRecipeWithUploadImage2(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('recipe id');
        $formData->image = $this->createImagePng(200, 200);
        $formData->image_remove = false;
        /** @var Recipe */
        $recipe = $this->getRecipesFixtures()->first();

        $this->formExtended
            ->expects($this->exactly(2))
            ->method('getData')
            ->willReturn($formData);

        $this->formExtended
            ->expects($this->once())
            ->method('uploadFiles')
            ->with($this->request, self::UPLOAD_RECIPES_PATH, []);

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipeByIdAndGroupIdOrFail')
            ->with($formData->id, null)
            ->willReturn($recipe);

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe);

        $this->object->__invoke($this->request, $this->formExtended, null);
    }

    #[Test]
    public function itShouldModifyASavedRecipeWithUploadImageRecipeAlreadyHasImage(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('recipe id');
        $formData->image = $this->createImagePng(200, 200);
        $formData->image_remove = false;
        /** @var Recipe */
        $recipe = $this->getRecipesFixtures()->first();
        $recipeImage = $this->createImagePng(200, 200);
        $imageUploaded = $recipeImage->move(self::UPLOAD_RECIPES_PATH);
        $recipe->setImage($imageUploaded->getFilename());

        $this->formExtended
            ->expects($this->exactly(2))
            ->method('getData')
            ->willReturn($formData);

        $this->formExtended
            ->expects($this->once())
            ->method('uploadFiles')
            ->with($this->request, self::UPLOAD_RECIPES_PATH, [$recipeImage->getFilename()]);

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipeByIdAndGroupIdOrFail')
            ->with($formData->id, null)
            ->willReturn($recipe);

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe);

        $this->object->__invoke($this->request, $this->formExtended, null);

        unlink(self::UPLOAD_RECIPES_PATH."/{$imageUploaded->getFilename()}");
    }

    #[Test]
    public function itShouldModifyASavedRecipeWithRemoveImage(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('recipe id');
        $formData->image_remove = true;
        /** @var Recipe */
        $recipe = $this->getRecipesFixtures()->first();
        $recipeImage = $this->createImagePng(200, 200);
        $imageUploaded = $recipeImage->move(self::UPLOAD_RECIPES_PATH);
        $recipe->setImage($imageUploaded->getFilename());

        $this->formExtended
            ->expects($this->exactly(2))
            ->method('getData')
            ->willReturn($formData);

        $this->formExtended
            ->expects($this->never())
            ->method('uploadFiles');

        $this->filesystem
            ->expects($this->once())
            ->method('remove')
            ->with(self::UPLOAD_RECIPES_PATH."/{$recipe->getImage()}");

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipeByIdAndGroupIdOrFail')
            ->with($formData->id, null)
            ->willReturn($recipe);

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe);

        $this->object->__invoke($this->request, $this->formExtended, null);

        unlink(self::UPLOAD_RECIPES_PATH."/{$imageUploaded->getFilename()}");
    }

    #[Test]
    public function itShouldFailModifyingRecipeNotFound(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('recipe id');

        $this->formExtended
            ->expects($this->once())
            ->method('getData')
            ->willReturn($formData);

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipeByIdAndGroupIdOrFail')
            ->with($formData->id, null)
            ->willThrowException(DBNotFoundException::fromMessage('recipe not found'));

        $this->recipeRepository
            ->expects($this->never())
            ->method('save');

        $this->expectException(RecipeModifyException::class);
        $this->object->__invoke($this->request, $this->formExtended, null);
    }

    #[Test]
    public function itShouldFailModifyingRecipeSaveException(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('recipe id');
        $recipe = $this->getRecipesFixtures()->first();

        $this->formExtended
            ->expects($this->exactly(2))
            ->method('getData')
            ->willReturn($formData);

        $this->formExtended
            ->expects($this->once())
            ->method('uploadFiles')
            ->with($this->request, self::UPLOAD_RECIPES_PATH, []);

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipeByIdAndGroupIdOrFail')
            ->with($formData->id, null)
            ->willReturn($recipe);

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe)
            ->willThrowException(new \Exception());

        $this->expectException(RecipeModifyException::class);
        $this->object->__invoke($this->request, $this->formExtended, null);
    }
}
