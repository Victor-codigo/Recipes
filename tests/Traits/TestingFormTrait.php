<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Common\RECIPE_TYPE;
use App\Entity\Recipe;
use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use App\Form\Recipe\RecipeModify\RECIPE_MODIFY_FORM_FIELDS;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Session\Session;
use VictorCodigo\SymfonyFormExtended\Form\FormMessage;

trait TestingFormTrait
{
    protected function assertRecipeFileIsUploaded(Recipe $recipeExpected, string $uploadPath, ?string $recipeFormerImageFileName): void
    {
        self::assertNotNull($recipeExpected->getImage());
        self::assertFileExists("{$uploadPath}/{$recipeExpected->getImage()}");

        if (null !== $recipeFormerImageFileName) {
            self::assertFileDoesNotExist("{$uploadPath}/{$recipeFormerImageFileName}");
        }
    }

    protected function assertRecipeFileIsNotUploaded(Recipe $recipeExpected, string $uploadPath): void
    {
        self::assertNull($recipeExpected->getImage());
        self::assertFileExists("{$uploadPath}/{$recipeExpected->getImage()}");
    }

    protected function assertRecipeFileIsRemoved(Recipe $recipeExpected, string $uploadPath, ?string $fileName): void
    {
        self::assertNull($recipeExpected->getImage());

        if (null !== $fileName) {
            self::assertFileDoesNotExist("{$uploadPath}/{$fileName}");
        }
    }

    /**
     * @param array{
     *      name: string,
     *      description?: string|null,
     *      preparation_time?: string|null,
     *      category: string,
     *      public?: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     * } $formData
     */
    protected function assertRecipeIsSavedInDataBase(Recipe $recipeExpected, array $formData): void
    {
        self::assertEquals($formData[RECIPE_CREATE_FORM_FIELDS::NAME->value], $recipeExpected->getName());
        self::assertEquals($formData[RECIPE_CREATE_FORM_FIELDS::DESCRIPTION->value] ?? null, $recipeExpected->getDescription());
        self::assertEquals(RECIPE_TYPE::tryFrom($formData[RECIPE_CREATE_FORM_FIELDS::CATEGORY->value]), $recipeExpected->getCategory());

        $preparationTime = null;
        if (isset($formData[RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value])) {
            $preparationTime = new \DateTimeImmutable('1970-01-01 '.$formData[RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value]);
        }

        self::assertEquals($preparationTime, $recipeExpected->getPreparationTime());
        self::assertEquals($formData[RECIPE_CREATE_FORM_FIELDS::PUBLIC->value] ?? false, $recipeExpected->getPublic());
        self::assertEquals($formData[RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value], $recipeExpected->getIngredients());
        self::assertEquals($formData[RECIPE_CREATE_FORM_FIELDS::STEPS->value], $recipeExpected->getSteps());
    }

    /**
     * @param array{
     *      name: string,
     *      description?: string|null,
     *      preparation_time?: string|null,
     *      category: string,
     *      public?: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     * } $formData
     */
    protected function assertRecipeIsNotSavedInDataBase(array $formData): void
    {
        $recipe = $this->recipeRepository->findOneBy(['name' => $formData[RECIPE_CREATE_FORM_FIELDS::NAME->value]]);
        $this->assertNull($recipe);
    }

    /**
     * @param array{
     *      id: string,
     *      name: string,
     *      description?: string|null,
     *      preparation_time?: string|null,
     *      category: string,
     *      public?: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>
     * } $formData
     */
    protected function assertRecipeIsModifiedInDataBase(Recipe $recipeExpected, array $formData): void
    {
        self::assertEquals($formData[RECIPE_MODIFY_FORM_FIELDS::ID->value], $recipeExpected->getId());
        self::assertEquals($formData[RECIPE_MODIFY_FORM_FIELDS::NAME->value], $recipeExpected->getName());
        self::assertEquals($formData[RECIPE_MODIFY_FORM_FIELDS::DESCRIPTION->value] ?? null, $recipeExpected->getDescription());
        self::assertEquals(RECIPE_TYPE::tryFrom($formData[RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value]), $recipeExpected->getCategory());

        $preparationTime = null;
        if (isset($formData[RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value])) {
            $preparationTime = new \DateTimeImmutable('1970-01-01 '.$formData[RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value]);
        }

        self::assertEquals($preparationTime, $recipeExpected->getPreparationTime());
        self::assertEquals($formData[RECIPE_MODIFY_FORM_FIELDS::PUBLIC->value] ?? false, $recipeExpected->getPublic());
        self::assertEquals($formData[RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value], $recipeExpected->getIngredients());
        self::assertEquals($formData[RECIPE_MODIFY_FORM_FIELDS::STEPS->value], $recipeExpected->getSteps());
    }

    /**
     * @param array<int, string> $messagesOkExpected
     */
    protected function assertResponseHasFlashMessageSuccess(string $flashBagMessageType, array $messagesOkExpected): void
    {
        /** @var Session */
        $session = $this->clientAuthenticated->getRequest()->getSession();
        /** @var array<int, FormMessage> */
        $messagesOk = $session->getFlashBag()->get($flashBagMessageType);
        $messagesText = array_map(
            fn (FormMessage $messageOk): string => $messageOk->template,
            $messagesOk
        );

        if (1 == count($messagesOkExpected) && 'validation.message' !== $messagesOkExpected[0]) {
            $this->assertEquals($messagesOkExpected, $messagesText);
        }
    }

    protected function assertResponseHasNotFlashMessageSuccess(string $flashBagMessageType): void
    {
        /** @var Session */
        $session = $this->clientAuthenticated->getRequest()->getSession();
        $this->assertEmpty($session->getFlashBag()->get($flashBagMessageType));
    }

    /**
     * @param array<int, string> $messagesErrorExpected
     */
    protected function assertResponseHasFlashMessageError(string $flashBagMessageType, array $messagesErrorExpected): void
    {
        /** @var Session */
        $session = $this->clientAuthenticated->getRequest()->getSession();
        /** @var array<int, FormMessage> */
        $messagesError = $session->getFlashBag()->get($flashBagMessageType);
        $messagesText = array_map(
            fn (FormMessage $messageError): string => $messageError->template,
            $messagesError
        );

        if (1 == count($messagesErrorExpected) && 'validation.message' !== $messagesErrorExpected[0]) {
            $this->assertEquals($messagesErrorExpected, $messagesText);
        }
    }

    protected function assertResponseHasNotFlashMessageError(string $flashBagMessageType): void
    {
        /** @var Session */
        $session = $this->clientAuthenticated->getRequest()->getSession();
        $this->assertEmpty($session->getFlashBag()->get($flashBagMessageType));
    }

    /**
     * @throws \Exception
     */
    protected function getFormCsrfToken(KernelBrowser $client, string $formUrl, string $tokenSelector): string
    {
        $pageCrawler = $client->request('GET', $formUrl);

        $csrfToken = $pageCrawler
            ->filter($tokenSelector)
            ->attr('value');

        if (null === $csrfToken) {
            throw new \Exception('CSRF token not found');
        }

        return $csrfToken;
    }
}
