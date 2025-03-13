<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Recipe\RecipeModify;

use App\Common\RECIPE_TYPE;
use App\Entity\Recipe;
use App\Form\Recipe\RecipeModify\RecipeModifyFormDataMapper;
use App\Form\Recipe\RecipeModify\RecipeModifyFormDataValidation;
use App\Tests\Traits\TestingRecipeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class RecipeModifyFormDataMapperTest extends TestCase
{
    use TestingRecipeTrait;

    private RecipeModifyFormDataMapper $object;

    protected function setUp(): void
    {
        parent::setUp();

        $this->object = new RecipeModifyFormDataMapper();
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

    private function modifyRecipeFromFormData(Recipe $recipe, RecipeModifyFormDataValidation $formData): Recipe
    {
        if (null !== $formData->image) {
            $image = $formData->image_remove ? null : $formData->image->getFilename();
        } else {
            $image = $recipe->getImage();
        }

        return new Recipe(
            $recipe->getId(),
            $recipe->getUser(),
            $recipe->getGroupId(),
            $formData->name,
            $formData->category->value,
            $formData->description,
            $formData->preparation_time,
            $formData->ingredients,
            $formData->steps,
            $image,
            $recipe->getRating(),
            $formData->public
        );
    }

    private function mapRecipeIntoRecipeCreateFormDataValidation(Recipe $recipe): RecipeModifyFormDataValidation
    {
        $formData = new RecipeModifyFormDataValidation();
        $formData->id = $recipe->getId();
        $formData->name = $recipe->getName();
        $formData->description = $recipe->getDescription();
        $formData->category = $recipe->getCategory();
        $formData->ingredients = $recipe->getIngredients();
        $formData->steps = $recipe->getSteps();
        $formData->public = $recipe->getPublic();
        $formData->preparation_time = $recipe->getPreparationTime();
        $formData->image = null === $recipe->getImage()
            ? null
            : new File((string) $recipe->getImage(), false);

        return $formData;
    }

    #[Test]
    public function itShouldMapFormInARecipeEntity(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('Recipe id');
        /** @var Recipe */
        $recipe = $this
            ->getRecipesFixtures()
            ->first();
        $recipeExpected = $this->modifyRecipeFromFormData($recipe, $formData);

        $this->object->mergeToEntity($recipe, $formData);

        $this->assertRecipesAreEqualCanonicalize(new ArrayCollection([$recipeExpected]), new ArrayCollection([$recipe]));
    }

    #[Test]
    public function itShouldMapFormInARecipeEntityFormDataImageUpload(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('Recipe id');
        $formData->image = new File('image.png', false);
        /** @var Recipe */
        $recipe = $this
            ->getRecipesFixtures()
            ->first();
        $recipeExpected = $this->modifyRecipeFromFormData($recipe, $formData);

        $this->object->mergeToEntity($recipe, $formData);

        $this->assertRecipesAreEqualCanonicalize(new ArrayCollection([$recipeExpected]), new ArrayCollection([$recipe]));
    }

    #[Test]
    public function itShouldMapFormInARecipeEntityFormDataImageIsNull(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('Recipe id');
        /** @var Recipe */
        $recipe = $this
            ->getRecipesFixtures()
            ->first();
        $recipe->setImage('image.png');
        $recipeExpected = $this->modifyRecipeFromFormData($recipe, $formData);

        $this->object->mergeToEntity($recipe, $formData);

        $this->assertRecipesAreEqualCanonicalize(new ArrayCollection([$recipeExpected]), new ArrayCollection([$recipe]));
    }

    #[Test]
    public function itShouldMapFormInARecipeEntityFormDataImageRemove(): void
    {
        $formData = $this->createRecipeFormDataValidationWithId('Recipe id');
        $formData->image_remove = true;
        $formData->image = new File('image.png', false);
        /** @var Recipe */
        $recipe = $this
            ->getRecipesFixtures()
            ->first();
        $recipeExpected = $this->modifyRecipeFromFormData($recipe, $formData);
        $recipeExpected->setImage(null);

        $this->object->mergeToEntity($recipe, $formData);

        $this->assertRecipesAreEqualCanonicalize(new ArrayCollection([$recipeExpected]), new ArrayCollection([$recipe]));
    }

    #[Test]
    public function itShouldMapEntityInAFormData(): void
    {
        /** @var Recipe */
        $recipe = $this
            ->getRecipesFixtures()
            ->first();
        $formDataExpected = $this->mapRecipeIntoRecipeCreateFormDataValidation($recipe);

        $return = $this->object->toForm($recipe);

        self::assertEquals($formDataExpected, $return);
    }

    #[Test]
    public function itShouldMapEntityInAFormDataRecipeImageIsNotNul(): void
    {
        /** @var Recipe */
        $recipe = $this
            ->getRecipesFixtures()
            ->get('recipe_6');
        $formDataExpected = $this->mapRecipeIntoRecipeCreateFormDataValidation($recipe);

        $return = $this->object->toForm($recipe);

        self::assertEquals($formDataExpected, $return);
    }
}
