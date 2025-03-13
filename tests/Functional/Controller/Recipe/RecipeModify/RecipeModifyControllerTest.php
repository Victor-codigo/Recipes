<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Recipe\RecipeModify;

use App\Common\RECIPE_TYPE;
use App\Controller\Recipe\RecipeModify\RecipeModifyController;
use App\Entity\Recipe;
use App\Form\Recipe\RecipeModify\RECIPE_MODIFY_FORM_FIELDS;
use App\Repository\RecipeRepository;
use App\Tests\Traits\TestingAliceBundleTrait;
use App\Tests\Traits\TestingFirewallTrait;
use App\Tests\Traits\TestingFormTrait;
use App\Tests\Traits\TestingImageTrait;
use App\Tests\Traits\TestingRecipeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class RecipeModifyControllerTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    use TestingFirewallTrait;
    use TestingFormTrait;
    use TestingRecipeTrait;
    use TestingImageTrait;
    use TestingAliceBundleTrait;

    private const string USER_LOGGED_ID = 'a9fee148-7b07-4c3e-8f8c-3cf8225a9bf6';
    private const string RECIPE_HOME_URL = '/en/recipe/page-1-100';
    private const string RECIPE_MODIFY_URL = '/en/recipe/modify';
    private const string LOGIN_URL = '/login';
    private const string UPLOAD_RECIPES_PATH = 'public/images/upload/recipe';

    private KernelBrowser $clientAuthenticated;
    private RecipeRepository $recipeRepository;
    private string $formCsrfToken;

    private function getClientAuthenticated(): void
    {
        $this->clientAuthenticated = $this->getNewClientAuthenticated(self::USER_LOGGED_ID);
        // @phpstan-ignore assign.propertyType
        $this->recipeRepository = $this->clientAuthenticated->getContainer()->get(RecipeRepository::class);
        $this->formCsrfToken = $this->getFormCsrfToken($this->clientAuthenticated, self::RECIPE_HOME_URL, '#'.RECIPE_MODIFY_FORM_FIELDS::FORM_NAME->value.' [name="'.RECIPE_MODIFY_FORM_FIELDS::getNameWithForm(RECIPE_MODIFY_FORM_FIELDS::CSRF_TOKEN).'"]');
    }

    /**
     * @param array{}|array{
     *      id: string,
     *      name: string,
     *      description?: string|null,
     *      preparation_time?: string|null,
     *      category: string,
     *      public?: bool,
     *      steps: array<int, string>,
     *      ingredients: array<int, string>,
     *      image_remove: bool,
     * } $request
     * @param array<string, UploadedFile> $files
     */
    private function sendRequest(KernelBrowser $client, string $formCsrfToken, array $request, array $files): void
    {
        $client->request('POST', self::RECIPE_MODIFY_URL, [
            RECIPE_MODIFY_FORM_FIELDS::FORM_NAME->value => [
                RECIPE_MODIFY_FORM_FIELDS::CSRF_TOKEN->value => $formCsrfToken,
                ...$request,
            ],
        ], [
            RECIPE_MODIFY_FORM_FIELDS::FORM_NAME->value => $files,
        ]);
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
     *      ingredients: array<int, string>,
     *      image_remove: bool,
     * } $request
     * @param array<string, UploadedFile> $files
     * @param array<int, string>          $messagesOk
     * @param array<int, string>          $messagesError
     */
    private function assertRecipeModifyIsOk(Recipe $recipeOriginal, string $recipeToModifyId, bool $validationOk, array $request, array $files, array $messagesOk, array $messagesError): void
    {
        /** @var Recipe */
        $recipeDataBase = $this->recipeRepository->findOneBy(['id' => $recipeToModifyId]);

        if ($validationOk) {
            $this->assertRecipeIsModifiedInDataBase($recipeDataBase, $request);
            $this->assertResponseHasFlashMessageSuccess(RecipeModifyController::FORM_FLASH_BAG_MESSAGES_SUCCESS, $messagesOk);
            $this->assertResponseHasNotFlashMessageError(RecipeModifyController::FORM_FLASH_BAG_MESSAGES_ERROR);

            if ($request[RECIPE_MODIFY_FORM_FIELDS::IMAGE_REMOVE->value]) {
                $this->assertRecipeFileIsRemoved($recipeDataBase, self::UPLOAD_RECIPES_PATH, $recipeOriginal->getImage());
            } elseif (!empty($files)) {
                $this->assertRecipeFileIsUploaded($recipeDataBase, self::UPLOAD_RECIPES_PATH, $recipeOriginal->getImage());
                unlink(self::UPLOAD_RECIPES_PATH."/{$recipeDataBase->getImage()}");
            }
        } else {
            $this->assertRecipesAreEqualCanonicalize(new ArrayCollection([$recipeOriginal]), new ArrayCollection([$recipeDataBase]));
            $this->assertResponseHasFlashMessageError(RecipeModifyController::FORM_FLASH_BAG_MESSAGES_ERROR, $messagesError);
            $this->assertResponseHasNotFlashMessageSuccess(RecipeModifyController::FORM_FLASH_BAG_MESSAGES_SUCCESS);

            if (null !== $recipeOriginal->getImage()) {
                $this->assertFileExists(self::UPLOAD_RECIPES_PATH."/{$recipeOriginal->getImage()}");
            }
        }
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
     *      ingredients: array<int, string>,
     *      image_remove: bool,
     * } $request
     * @param array<string, UploadedFile> $files
     * @param array<int, string>          $messagesOk
     * @param array<int, string>          $messagesError
     */
    #[Test]
    #[DataProviderExternal(RecipeModifyFormDataProvider::class, 'dataProvider')]
    public function itShouldValidateRecipeModifyForm(array $request, array $files, string $recipeToModifyId, bool $validationOk, array $messagesOk, array $messagesError): void
    {
        $this->getClientAuthenticated();

        /** @var Recipe */
        $recipeOriginal = $this->recipeRepository->findOneBy(['id' => $recipeToModifyId]);

        $this->sendRequest($this->clientAuthenticated, $this->formCsrfToken, $request, $files);

        $this->assertResponseRedirects(self::RECIPE_HOME_URL, Response::HTTP_SEE_OTHER);
        $this->assertRecipeModifyIsOk($recipeOriginal, $recipeToModifyId, $validationOk, $request, $files, $messagesOk, $messagesError);
    }

    #[Test]
    public function itShouldFailRecipeIdNotExists(): void
    {
        $this->getClientAuthenticated();

        $request = [
            RECIPE_MODIFY_FORM_FIELDS::ID->value => '4dc2491a-f105-4dd8-a0a1-6b9249f2eb69',
            RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
            RECIPE_MODIFY_FORM_FIELDS::DESCRIPTION->value => 'Recipe description test',
            RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
            RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => '2:00',
            RECIPE_MODIFY_FORM_FIELDS::PUBLIC->value => true,
            RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                'Ingredient 1',
                'Ingredient 2',
            ],
            RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                'Step 1',
                'Step 2',
            ],
            RECIPE_MODIFY_FORM_FIELDS::IMAGE_REMOVE->value => false,
        ];

        $this->sendRequest($this->clientAuthenticated, $this->formCsrfToken, $request, []);

        $this->assertResponseRedirects(self::RECIPE_HOME_URL, Response::HTTP_SEE_OTHER);
        $this->assertResponseHasFlashMessageError(RecipeModifyController::FORM_FLASH_BAG_MESSAGES_ERROR, ['Recipe not found']);
    }

    #[Test]
    public function itShouldRemoveRecipeImageRecipeAlreadyHasImage(): void
    {
        // Arrange
        $this->getClientAuthenticated();
        /** @var Recipe */
        $recipe = $this
            ->getRecipesFixtures()
            ->first();
        /** @var Recipe */
        $recipeOriginal = $this->recipeRepository->findOneBy(['id' => $recipe->getId()]);
        $recipeImage = $this->createImagePng(200, 200);
        $recipeImage->move(self::UPLOAD_RECIPES_PATH);
        $recipeOriginal->setImage($recipeImage->getFilename());
        $this->recipeRepository->save($recipeOriginal);

        $request = [
            RECIPE_MODIFY_FORM_FIELDS::ID->value => $recipe->getId(),
            RECIPE_MODIFY_FORM_FIELDS::NAME->value => 'Recipe name test',
            RECIPE_MODIFY_FORM_FIELDS::DESCRIPTION->value => 'Recipe description test',
            RECIPE_MODIFY_FORM_FIELDS::CATEGORY->value => RECIPE_TYPE::NO_CATEGORY->value,
            RECIPE_MODIFY_FORM_FIELDS::PREPARATION_TIME->value => '2:00',
            RECIPE_MODIFY_FORM_FIELDS::PUBLIC->value => true,
            RECIPE_MODIFY_FORM_FIELDS::INGREDIENTS->value => [
                'Ingredient 1',
                'Ingredient 2',
            ],
            RECIPE_MODIFY_FORM_FIELDS::STEPS->value => [
                'Step 1',
                'Step 2',
            ],
            RECIPE_MODIFY_FORM_FIELDS::IMAGE_REMOVE->value => true,
        ];

        // Action
        $this->sendRequest($this->clientAuthenticated, $this->formCsrfToken, $request, []);

        // Assert
        $this->assertResponseRedirects(self::RECIPE_HOME_URL, Response::HTTP_SEE_OTHER);
        $this->assertRecipeModifyIsOk($recipeOriginal, $recipeOriginal->getId(), true, $request, [], ['form.validation.msg.ok'], []);
    }

    #[Test]
    public function itShouldFailUnauthorized(): void
    {
        $client = self::createClient();

        $this->sendRequest($client, '', [], []);

        $this->assertResponseRedirects(self::LOGIN_URL, Response::HTTP_FOUND);
    }
}
