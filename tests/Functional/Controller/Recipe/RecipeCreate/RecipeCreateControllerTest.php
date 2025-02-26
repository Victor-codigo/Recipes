<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Recipe\RecipeCreate;

use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use App\Repository\RecipeRepository;
use App\Tests\Traits\TestingFirewallTrait;
use App\Tests\Traits\TestingFormTrait;
use App\Tests\Traits\TestingImageTrait;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class RecipeCreateControllerTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    use TestingFirewallTrait;
    use TestingFormTrait;
    use TestingImageTrait;

    private const string USER_LOGGED_ID = 'a9fee148-7b07-4c3e-8f8c-3cf8225a9bf6';
    private const string RECIPE_CREATE_URL = '/en/recipe/create';
    private const string RECIPE_HOME_URL = '/en/recipe/page-1-100';
    private const string UPLOAD_RECIPES_PATH = 'images/upload/recipe';

    private KernelBrowser $clientAuthenticated;
    private RecipeRepository $recipeRepository;
    private string $formCsrfToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientAuthenticated = $this->getNewClientAuthenticated(self::USER_LOGGED_ID);
        // @phpstan-ignore assign.propertyType
        $this->recipeRepository = $this->clientAuthenticated->getContainer()->get(RecipeRepository::class);
        $this->formCsrfToken = $this->getFormCsrfToken(self::RECIPE_HOME_URL, '#'.RECIPE_CREATE_FORM_FIELDS::FORM_NAME->value.' [name="'.RECIPE_CREATE_FORM_FIELDS::getNameWithForm(RECIPE_CREATE_FORM_FIELDS::CSRF_TOKEN).'"]');
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
     * } $request
     * @param array<string, UploadedFile> $files
     */
    #[Test]
    #[DataProviderExternal(RecipeCreateFormDataProvider::class, 'dataProvider')]
    public function itShouldValidateRecipeCreateForm(array $request, array $files, bool $validationOk): void
    {
        $this->clientAuthenticated->request('POST', self::RECIPE_CREATE_URL, [
            RECIPE_CREATE_FORM_FIELDS::FORM_NAME->value => [
                RECIPE_CREATE_FORM_FIELDS::CSRF_TOKEN->value => $this->formCsrfToken,
                ...$request,
            ],
        ], [
            RECIPE_CREATE_FORM_FIELDS::FORM_NAME->value => $files,
        ]);

        $this->assertResponseRedirects(self::RECIPE_HOME_URL, Response::HTTP_SEE_OTHER);

        if ($validationOk) {
            $this->assertRecipeIsSavedInDataBaseAndRemoveImage($request, self::UPLOAD_RECIPES_PATH, !empty($files));
            $this->assertResponseHasFlashMessageSuccess();
            $this->assertResponseHasNotFlashMessageError();
        } else {
            $this->assertRecipeIsNotSavedInDataBase($request);
            $this->assertResponseHasFlashMessageError();
            $this->assertResponseHasNotFlashMessageSuccess();
        }
    }
}
