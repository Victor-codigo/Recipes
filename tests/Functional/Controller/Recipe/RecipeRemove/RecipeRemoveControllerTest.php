<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Recipe\RecipeRemove;

use App\Controller\Recipe\RecipeRemove\RecipeRemoveController;
use App\Entity\Recipe;
use App\Form\Recipe\RecipeRemove\RECIPE_REMOVE_FORM_FIELDS;
use App\Repository\RecipeRepository;
use App\Tests\Traits\TestingFirewallTrait;
use App\Tests\Traits\TestingFormTrait;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RecipeRemoveControllerTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    use TestingFirewallTrait;
    use TestingFormTrait;

    private const string USER_LOGGED_ID = 'a9fee148-7b07-4c3e-8f8c-3cf8225a9bf6';
    private const string RECIPE_REMOVE_FORM_URL = '/en/recipe/remove';
    private const string RECIPE_HOME_URL = '/en/recipe/page-1-100';

    private KernelBrowser $clientAuthenticated;
    private RecipeRepository $recipeRepository;
    private string $formCsrfToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientAuthenticated = $this->getNewClientAuthenticated(self::USER_LOGGED_ID);
        // @phpstan-ignore assign.propertyType
        $this->recipeRepository = $this->clientAuthenticated->getContainer()->get(RecipeRepository::class);
        $this->formCsrfToken = $this->getFormCsrfToken($this->clientAuthenticated, self::RECIPE_HOME_URL, '#'.RECIPE_REMOVE_FORM_FIELDS::FORM_NAME->value.' [name="'.RECIPE_REMOVE_FORM_FIELDS::getNameWithForm(RECIPE_REMOVE_FORM_FIELDS::CSRF_TOKEN).'"]');
    }

    /**
     * @param array{
     *      recipes_id: array<int, string>,
     * } $request
     * @param array<int, string> $recipesExpectedNotToBeRemovedId
     * @param array<int, string> $messagesOk
     * @param array<int, string> $messagesError
     */
    #[Test]
    #[DataProviderExternal(RecipeRemoveFormDataProvider::class, 'dataProvider')]
    public function itShouldRemoveRecipes(array $request, array $recipesExpectedNotToBeRemovedId, bool $validationOk, array $messagesOk, array $messagesError): void
    {
        $this->clientAuthenticated->request('POST', self::RECIPE_REMOVE_FORM_URL, [
            RECIPE_REMOVE_FORM_FIELDS::FORM_NAME->value => [
                RECIPE_REMOVE_FORM_FIELDS::CSRF_TOKEN->value => $this->formCsrfToken,
                ...$request,
            ],
        ]);

        $this->assertResponseRedirects(self::RECIPE_HOME_URL, Response::HTTP_SEE_OTHER);
        $this->assertRecipesAreRemoved($request, $recipesExpectedNotToBeRemovedId, $validationOk, $messagesOk, $messagesError);
    }

    /**
     * @param array{
     *      recipes_id: array<int, string>,
     * } $request
     * @param array<int, string> $recipesExpectedNotToBeRemovedId
     * @param array<int, string> $messagesOk
     * @param array<int, string> $messagesError
     */
    private function assertRecipesAreRemoved(array $request, array $recipesExpectedNotToBeRemovedId, bool $validationOk, array $messagesOk, array $messagesError): void
    {
        $recipesFoundInDb = $this->recipeRepository->findBy(['id' => $request['recipes_id']]);
        $recipesFoundInDbIds = array_map(
            fn (Recipe $recipe): string => $recipe->getId(),
            $recipesFoundInDb
        );

        self::assertEqualsCanonicalizing($recipesExpectedNotToBeRemovedId, $recipesFoundInDbIds);

        if ($validationOk) {
            $this->assertResponseHasFlashMessageSuccess(RecipeRemoveController::FORM_FLASH_BAG_MESSAGES_SUCCESS, $messagesOk);
            $this->assertResponseHasNotFlashMessageError(RecipeRemoveController::FORM_FLASH_BAG_MESSAGES_ERROR);
        } else {
            $this->assertResponseHasNotFlashMessageSuccess(RecipeRemoveController::FORM_FLASH_BAG_MESSAGES_SUCCESS);
            $this->assertResponseHasFlashMessageError(RecipeRemoveController::FORM_FLASH_BAG_MESSAGES_ERROR, $messagesError);
        }
    }
}
