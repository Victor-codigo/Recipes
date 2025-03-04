<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Common\RECIPE_TYPE;
use App\Controller\Recipe\RecipeCreate\RecipeCreateController;
use App\Entity\Recipe;
use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use Symfony\Component\HttpFoundation\Session\Session;
use VictorCodigo\SymfonyFormExtended\Form\FormMessage;

trait TestingFormTrait
{
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
    protected function assertRecipeIsSavedInDataBaseAndRemoveImage(array $formData, string $uploadPath, bool $fileUploaded): void
    {
        /** @var Recipe|null */
        $recipe = $this->recipeRepository->findOneBy(['name' => $formData[RECIPE_CREATE_FORM_FIELDS::NAME->value]]);

        self::assertNotNull($recipe);
        self::assertEquals($formData[RECIPE_CREATE_FORM_FIELDS::NAME->value], $recipe->getName());
        self::assertEquals($formData[RECIPE_CREATE_FORM_FIELDS::DESCRIPTION->value] ?? null, $recipe->getDescription());
        self::assertEquals(RECIPE_TYPE::tryFrom($formData[RECIPE_CREATE_FORM_FIELDS::CATEGORY->value]), $recipe->getCategory());

        $preparationTime = null;
        if (isset($formData[RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value])) {
            $preparationTime = new \DateTimeImmutable('1970-01-01 '.$formData[RECIPE_CREATE_FORM_FIELDS::PREPARATION_TIME->value]);
        }

        self::assertEquals($preparationTime, $recipe->getPreparationTime());
        self::assertEquals($formData[RECIPE_CREATE_FORM_FIELDS::PUBLIC->value] ?? false, $recipe->getPublic());
        self::assertEquals($formData[RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value], $recipe->getIngredients());
        self::assertEquals($formData[RECIPE_CREATE_FORM_FIELDS::STEPS->value], $recipe->getSteps());

        if ($fileUploaded) {
            self::assertNotNull($recipe->getImage());
            self::assertFileExists("{$uploadPath}/{$recipe->getImage()}");
        }

        if (!$fileUploaded) {
            self::assertNull($recipe->getImage());
        }

        unlink("{$uploadPath}/{$recipe->getImage()}");
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
     * @param array<int, string> $messagesOkExpected
     */
    protected function assertResponseHasFlashMessageSuccess(array $messagesOkExpected): void
    {
        /** @var Session */
        $session = $this->clientAuthenticated->getRequest()->getSession();
        /** @var array<int, FormMessage> */
        $messagesOk = $session->getFlashBag()->get(RecipeCreateController::FORM_FLASH_BAG_MESSAGES_SUCCESS);
        $messagesText = array_map(
            fn (FormMessage $messageOk): string => $messageOk->template,
            $messagesOk
        );

        if (1 == count($messagesOkExpected) && 'validation.message' !== $messagesOkExpected[0]) {
            $this->assertEquals($messagesOkExpected, $messagesText);
        }
    }

    protected function assertResponseHasNotFlashMessageSuccess(): void
    {
        /** @var Session */
        $session = $this->clientAuthenticated->getRequest()->getSession();
        $this->assertEmpty($session->getFlashBag()->get(RecipeCreateController::FORM_FLASH_BAG_MESSAGES_SUCCESS));
    }

    /**
     * @param array<int, string> $messagesErrorExpected
     */
    protected function assertResponseHasFlashMessageError(array $messagesErrorExpected): void
    {
        /** @var Session */
        $session = $this->clientAuthenticated->getRequest()->getSession();
        /** @var array<int, FormMessage> */
        $messagesError = $session->getFlashBag()->get(RecipeCreateController::FORM_FLASH_BAG_MESSAGES_ERROR);
        $messagesText = array_map(
            fn (FormMessage $messageError): string => $messageError->template,
            $messagesError
        );

        if (1 == count($messagesErrorExpected) && 'validation.message' !== $messagesErrorExpected[0]) {
            $this->assertEquals($messagesErrorExpected, $messagesText);
        }
    }

    protected function assertResponseHasNotFlashMessageError(): void
    {
        /** @var Session */
        $session = $this->clientAuthenticated->getRequest()->getSession();
        $this->assertEmpty($session->getFlashBag()->get(RecipeCreateController::FORM_FLASH_BAG_MESSAGES_ERROR));
    }

    /**
     * @throws \Exception
     */
    protected function getFormCsrfToken(string $formUrl, string $tokenSelector): string
    {
        $pageCrawler = $this->clientAuthenticated->request('GET', $formUrl);

        $csrfToken = $pageCrawler
            ->filter($tokenSelector)
            ->attr('value');

        if (null === $csrfToken) {
            throw new \Exception('CSRF token not found');
        }

        return $csrfToken;
    }
}
