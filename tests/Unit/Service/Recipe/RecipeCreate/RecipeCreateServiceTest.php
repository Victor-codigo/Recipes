<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Recipe\RecipeCreate;

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

class RecipeCreateServiceTest extends TestCase
{
    use TestingRecipeTrait;
    use TestingUserTrait;

    private RecipeCreateService $object;
    private RecipeRepository&MockObject $recipeRepository;
    private Security&MockObject $security;
    private RecipeCreateFormDataMapper&MockObject $recipeCreateFormDataMapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recipeRepository = $this->createMock(RecipeRepository::class);
        $this->recipeCreateFormDataMapper = $this->createMock(RecipeCreateFormDataMapper::class);
        $this->security = $this->createMock(Security::class);
        $this->object = new RecipeCreateService(
            $this->recipeRepository,
            $this->security,
            $this->recipeCreateFormDataMapper
        );
    }

    #[Test]
    public function itShouldCreateARecipe(): void
    {
        $user = $this->getUsersFixtures()->first();
        $recipe = $this->getRecipesFixtures()->first();
        $recipeCreateFormDataValidation = $this->createRecipeFormDataValidation();
        $groupId = 'recipe group id';
        $recipeId = 'recipe id';

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

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe);

        $this->object->__invoke($recipeCreateFormDataValidation, $groupId);
    }

    #[Test]
    public function itShouldFailCreatingARecipeUserSessionIsNull(): void
    {
        $recipeCreateFormDataValidation = $this->createRecipeFormDataValidation();
        $groupId = 'recipe group id';

        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $this->recipeRepository
            ->expects($this->never())
            ->method('uuidCreate');

        $this->recipeCreateFormDataMapper
            ->expects($this->never())
            ->method('toEntity');

        $this->recipeRepository
            ->expects($this->never())
            ->method('save');

        $this->expectException(RecipeCreateException::class);
        $this->object->__invoke($recipeCreateFormDataValidation, $groupId);
    }

    #[Test]
    public function itShouldFailCreatingARecipeErrorSaving(): void
    {
        $user = $this->getUsersFixtures()->first();
        $recipe = $this->getRecipesFixtures()->first();
        $recipeCreateFormDataValidation = $this->createRecipeFormDataValidation();
        $groupId = 'recipe group id';
        $recipeId = 'recipe id';

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

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe)
            ->willThrowException(new \Exception('Error saving recipe'));

        $this->expectException(\Exception::class);
        $this->object->__invoke($recipeCreateFormDataValidation, $groupId);
    }
}
