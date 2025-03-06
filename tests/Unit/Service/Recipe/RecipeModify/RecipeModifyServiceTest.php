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
use App\Tests\Traits\TestingRecipeTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RecipeModifyServiceTest extends TestCase
{
    use TestingAliceBundleTrait;
    use TestingRecipeTrait;
    use TestingFixturesTrait;

    private RecipeModifyService $object;
    private RecipeRepository&MockObject $recipeRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recipeRepository = $this->createMock(RecipeRepository::class);
        $this->object = new RecipeModifyService($this->recipeRepository, new RecipeModifyFormDataMapper());
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
        $recipeFormValidationData->image_remove = true;
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

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipeByIdOrFail')
            ->with($formData->id)
            ->willReturn($recipe);

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe);

        $this->object->__invoke($formData);
    }

    #[Test]
    public function itShouldFailModifyingRecipeNotFound(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('recipe id');

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipeByIdOrFail')
            ->with($formData->id)
            ->willThrowException(DBNotFoundException::fromMessage('recipe not found'));

        $this->recipeRepository
            ->expects($this->never())
            ->method('save');

        $this->expectException(RecipeModifyException::class);
        $this->object->__invoke($formData);
    }

    #[Test]
    public function itShouldFailModifyingRecipeSaveException(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('recipe id');
        $recipe = $this->getRecipesFixtures()->first();

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipeByIdOrFail')
            ->with($formData->id)
            ->willReturn($recipe);

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe)
            ->willThrowException(new \Exception());

        $this->expectException(RecipeModifyException::class);
        $this->object->__invoke($formData);
    }
}
