<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Recipe\RecipeCreate;

use App\Form\Recipe\RecipeCreate\RecipeCreateFormDataMapper;
use App\Repository\RecipeRepository;
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

    private RecipeCreateService $object;
    private RecipeRepository&MockObject $recipeRepository;
    private Security&MockObject $security;
    private RecipeCreateFormDataMapper&MockObject $recipeCreateFormDataMapper;
    private Request&MockObject $request;
    private FormExtendedInterface&MockObject $form;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = $this->createMock(Request::class);
        $this->form = $this->createMock(FormExtendedInterface::class);
        $this->recipeRepository = $this->createMock(RecipeRepository::class);
        $this->recipeCreateFormDataMapper = $this->createMock(RecipeCreateFormDataMapper::class);
        $this->security = $this->createMock(Security::class);
        $this->object = new RecipeCreateService(
            $this->recipeRepository,
            $this->security,
            $this->recipeCreateFormDataMapper,
            self::RECIPE_UPLOAD_PATH
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

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe);

        $this->object->__invoke($this->request, $this->form, $groupId);
    }

    #[Test]
    public function itShouldFailCreatingARecipeErrorSaving(): void
    {
        $user = $this->getUsersFixtures()->first();
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

        $this->recipeRepository
            ->expects($this->once())
            ->method('save')
            ->with($recipe)
            ->willThrowException(new \Exception('Error saving recipe'));

        $this->expectException(\Exception::class);
        $this->object->__invoke($this->request, $this->form, $groupId);
    }
}
