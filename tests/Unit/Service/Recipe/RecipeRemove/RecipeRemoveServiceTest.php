<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Recipe\RecipeRemove;

use App\Entity\Recipe;
use App\Form\Recipe\RecipeRemove\RecipeRemoveFormDataMapper;
use App\Form\Recipe\RecipeRemove\RecipeRemoveFormDataValidation;
use App\Repository\Exception\DBNotFoundException;
use App\Repository\RecipeRepository;
use App\Service\Exception\RecipeRemoveException;
use App\Service\Recipe\RecipeRemove\RecipeRemoveService;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use VictorCodigo\SymfonyFormExtended\Form\FormExtendedInterface;

class RecipeRemoveServiceTest extends TestCase
{
    private const string UPLOAD_RECIPES_PATH = 'public/images/upload/recipe';

    private RecipeRemoveService $object;
    private RecipeRepository&MockObject $recipeRepository;
    private recipeRemoveFormDataMapper&MockObject $recipeRemoveFormDataMapper;
    private FormExtendedInterface&MockObject $form;
    private Filesystem&MockObject $fileSystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recipeRepository = $this->createMock(RecipeRepository::class);
        $this->form = $this->createMock(FormExtendedInterface::class);
        $this->recipeRemoveFormDataMapper = $this->createMock(RecipeRemoveFormDataMapper::class);
        $this->fileSystem = $this->createMock(Filesystem::class);

        $this->object = new RecipeRemoveService(
            $this->recipeRepository,
            $this->recipeRemoveFormDataMapper,
            $this->fileSystem,
            self::UPLOAD_RECIPES_PATH
        );
    }

    private function createRecipeMockWithImage(?string $image): Recipe
    {
        $recipe = $this->createMock(Recipe::class);
        $recipe
            ->expects($this->any())
            ->method('getImage')
            ->willReturn($image);

        return $recipe;
    }

    #[Test]
    public function itShouldRemoveOneRecipe(): void
    {
        $formData = new RecipeRemoveFormDataValidation();
        $formData->recipes_id = ['recipe id 1'];
        $recipesIdCollection = new ArrayCollection($formData->recipes_id);
        $groupId = null;
        $recipesFoundInDb = new ArrayCollection([$this->createRecipeMockWithImage('imageFileName.txt')]);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($formData);

        $this->recipeRemoveFormDataMapper
            ->expects($this->once())
            ->method('toRecipesIdCollection')
            ->with($formData)
            ->willReturn($recipesIdCollection);

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipesByIdAndGroupIdOrFail')
            ->with($recipesIdCollection, $groupId)
            ->willReturn($recipesFoundInDb);

        $this->recipeRepository
            ->expects($this->once())
            ->method('remove')
            ->with($recipesFoundInDb);

        $removeInvokedTimes = $this->once();
        $this->fileSystem
            ->expects($removeInvokedTimes)
            ->method('remove')
            ->willReturnCallback(function (string $imageToRemove) use ($recipesFoundInDb, $removeInvokedTimes) {
                $invocationTime = $removeInvokedTimes->numberOfInvocations();
                /** @var Recipe */
                $recipeExpected = $recipesFoundInDb->get($invocationTime - 1);
                $imageToRemovePathExpected = self::UPLOAD_RECIPES_PATH."/{$recipeExpected->getImage()}";

                self::assertEquals($imageToRemovePathExpected, $imageToRemove);
            });

        $this->object->__invoke($this->form, null);
    }

    #[Test]
    public function itShouldRemoveManyRecipes(): void
    {
        $formData = new RecipeRemoveFormDataValidation();
        $formData->recipes_id = [
            'recipe id 1',
            'recipe id 2',
            'recipe id 3',
        ];
        $recipesIdCollection = new ArrayCollection($formData->recipes_id);
        $groupId = null;
        $recipesFoundInDb = new ArrayCollection([
            $this->createRecipeMockWithImage('image1.png'),
            $this->createRecipeMockWithImage(null),
            $this->createRecipeMockWithImage('image3.png'),
        ]);
        $recipesFoundInDbWithImage = new ArrayCollection([
            $recipesFoundInDb->get(0),
            $recipesFoundInDb->get(2),
        ]);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($formData);

        $this->recipeRemoveFormDataMapper
            ->expects($this->once())
            ->method('toRecipesIdCollection')
            ->with($formData)
            ->willReturn($recipesIdCollection);

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipesByIdAndGroupIdOrFail')
            ->with($recipesIdCollection, $groupId)
            ->willReturn($recipesFoundInDb);

        $this->recipeRepository
            ->expects($this->once())
            ->method('remove')
            ->with($recipesFoundInDb);

        $removeInvokedTimes = $this->exactly(2);
        $this->fileSystem
            ->expects($removeInvokedTimes)
            ->method('remove')
            ->willReturnCallback(function (string $imageToRemove) use ($recipesFoundInDbWithImage, $removeInvokedTimes) {
                $invocationTime = $removeInvokedTimes->numberOfInvocations();
                /** @var Recipe */
                $recipeExpected = $recipesFoundInDbWithImage->get($invocationTime - 1);
                $imageToRemovePathExpected = self::UPLOAD_RECIPES_PATH."/{$recipeExpected->getImage()}";

                self::assertEquals($imageToRemovePathExpected, $imageToRemove);
            });

        $this->object->__invoke($this->form, null);
    }

    #[Test]
    public function itShouldFailRemovingRecipesRecipesNotFound(): void
    {
        $formData = new RecipeRemoveFormDataValidation();
        $formData->recipes_id = [
            'recipe id 1',
            'recipe id 2',
            'recipe id 3',
        ];
        $recipesIdCollection = new ArrayCollection($formData->recipes_id);
        $groupId = null;

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($formData);

        $this->recipeRemoveFormDataMapper
            ->expects($this->once())
            ->method('toRecipesIdCollection')
            ->with($formData)
            ->willReturn($recipesIdCollection);

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipesByIdAndGroupIdOrFail')
            ->with($recipesIdCollection, $groupId)
            ->willThrowException(DBNotFoundException::fromMessage('Not found'));

        $this->recipeRepository
            ->expects($this->never())
            ->method('remove');

        $this->fileSystem
            ->expects($this->never())
            ->method('remove');

        $this->expectException(RecipeRemoveException::class);
        $this->object->__invoke($this->form, null);
    }

    #[Test]
    public function itShouldFailRemovingRecipesRemoveError(): void
    {
        $formData = new RecipeRemoveFormDataValidation();
        $formData->recipes_id = [
            'recipe id 1',
            'recipe id 2',
            'recipe id 3',
        ];
        $recipesIdCollection = new ArrayCollection($formData->recipes_id);
        $groupId = null;
        $recipesFoundInDb = new ArrayCollection([
            $this->createMock(Recipe::class),
            $this->createMock(Recipe::class),
            $this->createMock(Recipe::class),
        ]);

        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($formData);

        $this->recipeRemoveFormDataMapper
            ->expects($this->once())
            ->method('toRecipesIdCollection')
            ->with($formData)
            ->willReturn($recipesIdCollection);

        $this->recipeRepository
            ->expects($this->once())
            ->method('findRecipesByIdAndGroupIdOrFail')
            ->with($recipesIdCollection, $groupId)
            ->willReturn($recipesFoundInDb);

        $this->recipeRepository
            ->expects($this->once())
            ->method('remove')
            ->with($recipesFoundInDb)
            ->willThrowException(new \Exception());

        $this->fileSystem
            ->expects($this->never())
            ->method('remove');

        $this->expectException(RecipeRemoveException::class);
        $this->object->__invoke($this->form, null);
    }
}
