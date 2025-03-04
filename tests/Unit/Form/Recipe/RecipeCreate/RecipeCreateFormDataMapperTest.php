<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Recipe\RecipeCreate;

use App\Entity\User;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormDataMapper;
use App\Tests\Traits\TestingRecipeTrait;
use App\Tests\Traits\TestingUserTrait;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RecipeCreateFormDataMapperTest extends TestCase
{
    use TestingRecipeTrait;
    use TestingUserTrait;

    private RecipeCreateFormDataMapper $object;

    protected function setUp(): void
    {
        parent::setUp();

        $this->object = new RecipeCreateFormDataMapper();
    }

    #[Test]
    public function itShouldCreateAEntity(): void
    {
        $recipeCreateFormDataValidation = $this->createRecipeFormDataValidation();
        /** @var User */
        $user = $this->getUsersFixtures()->first() ?: throw new \Exception('User not found');
        $recipeId = 'recipe id';
        $groupId = 'group id';

        $return = $this->object->toEntity($recipeCreateFormDataValidation, $user, $recipeId, $groupId);

        static::assertEquals($recipeId, $return->getId());
        static::assertEquals($user->getId(), $return->getUserId());
        static::assertEquals($groupId, $return->getGroupId());
        static::assertEquals($recipeCreateFormDataValidation->name, $return->getName());
        static::assertEquals($recipeCreateFormDataValidation->category, $return->getCategory());
        static::assertEquals($recipeCreateFormDataValidation->description, $return->getDescription());
        static::assertEquals($recipeCreateFormDataValidation->preparation_time, $return->getPreparationTime());
        static::assertEquals($recipeCreateFormDataValidation->ingredients, $return->getIngredients());
        static::assertEquals($recipeCreateFormDataValidation->steps, $return->getSteps());
        static::assertEquals($recipeCreateFormDataValidation->image, $return->getImage());
        static::assertNull($return->getRating());
        static::assertFalse($return->getPublic());
        static::assertEqualsWithDelta(new \DateTimeImmutable(), $return->getCreatedOn(), 1);
    }

    #[Test]
    public function itShouldCreateARecipeCreateFormDataValidation(): void
    {
        $recipe = $this->getRecipesFixtures()->first() ?: throw new \Exception('Recipe not found');

        $return = $this->object->toForm($recipe);

        static::assertEquals($recipe->getName(), $return->name);
        static::assertEquals($recipe->getDescription(), $return->description);
        static::assertEquals($recipe->getIngredients(), $return->ingredients);
        static::assertEquals($recipe->getSteps(), $return->steps);
        static::assertEquals($recipe->getImage(), $return->image);
        static::assertEquals($recipe->getPreparationTime(), $return->preparation_time);
        static::assertEquals($recipe->getCategory(), $return->category);
        static::assertEquals($recipe->getPublic(), $return->public);
    }
}
