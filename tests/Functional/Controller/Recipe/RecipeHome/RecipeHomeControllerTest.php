<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Recipe\RecipeHome;

use App\Form\HomeSection\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Repository\RecipeRepository;
use App\Tests\Traits\TestingFirewallTrait;
use App\Tests\Traits\TestingFormTrait;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RecipeHomeControllerTest extends WebTestCase
{
    use ReloadDatabaseTrait;
    use TestingFirewallTrait;
    use TestingFormTrait;

    private const string USER_LOGGED_ID = 'a9fee148-7b07-4c3e-8f8c-3cf8225a9bf6';
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
        $this->formCsrfToken = $this->getFormCsrfToken($this->clientAuthenticated, self::RECIPE_HOME_URL, '#'.SEARCHBAR_FORM_FIELDS::FORM_NAME->value.' [name="'.SEARCHBAR_FORM_FIELDS::getNameWithForm(SEARCHBAR_FORM_FIELDS::CSRF_TOKEN).'"]');
    }

    /**
     * @param array{
     *    field_filter: string,
     *    name_filter: string,
     *    search_value: string,
     *  } $request
     * @param array<int, string> $recipesFoundExpected
     */
    #[Test]
    #[DataProviderExternal(recipeFindFormDataProvider::class, 'dataProvider')]
    public function itShouldFindRecipesForm(array $request, array $recipesFoundExpected): void
    {
        $this->clientAuthenticated->request('POST', self::RECIPE_HOME_URL, [
            SEARCHBAR_FORM_FIELDS::FORM_NAME->value => [
                SEARCHBAR_FORM_FIELDS::CSRF_TOKEN->value => $this->formCsrfToken,
                ...$request,
            ],
        ]);

        $this->assertRecipesExpectedAreFound($recipesFoundExpected);
    }

    /**
     * @param array<int, string> $recipesFoundExpected
     */
    private function assertRecipesExpectedAreFound(array $recipesFoundExpected): void
    {
        $recipeTags = $this->clientAuthenticated
            ->getCrawler()
            ->filter('[data-js-list-item] [data-js-item-name]');

        $this->assertResponseStatusCodeSame(Response::HTTP_SEE_OTHER);
        self::assertCount(count($recipesFoundExpected), $recipeTags);

        foreach ($recipeTags->getIterator() as $recipeTag) {
            self::assertContains(trim($recipeTag->textContent), $recipesFoundExpected);
        }
    }
}
