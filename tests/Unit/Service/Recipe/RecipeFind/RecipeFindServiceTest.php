<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Recipe\RecipeFind;

use App\Common\RECIPE_TYPE;
use App\Form\HomeSection\SearchBar\FIELD_FILTERS;
use App\Form\HomeSection\SearchBar\SEARCH_TEXT_FILTER;
use App\Form\HomeSection\SearchBar\SearchFormDataValidation;
use App\Repository\Exception\DBNotFoundException;
use App\Repository\RecipeRepository;
use App\Repository\RecipeSearchCriteria;
use App\Service\Recipe\RecipeFind\RecipeFindService;
use App\Service\Recipe\RecipeFind\RecipeFindServiceDto;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RecipeFindServiceTest extends TestCase
{
    private RecipeFindService $object;
    private RecipeRepository&MockObject $recipeRepository;
    private RecipeSearchCriteria&MockObject $recipeSearchCriteria;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recipeRepository = $this->createMock(RecipeRepository::class);
        $this->recipeSearchCriteria = $this
            ->getMockBuilder(RecipeSearchCriteria::class)
            ->getMock();
        $this->object = new RecipeFindService($this->recipeRepository, $this->recipeSearchCriteria);
    }

    private function MockCreateCriteriaSearch(RecipeFindServiceDto $recipesFilters): void
    {
        $this->recipeSearchCriteria
            ->expects(self::once())
            ->method('addUserId')
            ->with($recipesFilters->userId)
            ->willReturn($this->recipeSearchCriteria);

        $this->recipeSearchCriteria
            ->expects(self::once())
            ->method('addGroupId')
            ->with($recipesFilters->groupId)
            ->willReturn($this->recipeSearchCriteria);

        $this->recipeSearchCriteria
            ->expects(self::once())
            ->method('addPublic')
            ->with($recipesFilters->public)
            ->willReturn($this->recipeSearchCriteria);

        $this->recipeSearchCriteria
            ->expects(self::once())
            ->method('setPagination')
            ->with($recipesFilters->page, $recipesFilters->pageItems)
            ->willReturn($this->recipeSearchCriteria);

        $this->recipeSearchCriteria
            ->expects(self::once())
            ->method('setOrderByRecipeName');

        if (FIELD_FILTERS::NAME === $recipesFilters->searchFormData?->field_filter) {
            $this->recipeSearchCriteria
                ->expects(self::once())
                ->method('addName')
                ->with($recipesFilters->searchFormData->search_value, $recipesFilters->searchFormData->name_filter);
        }

        if (FIELD_FILTERS::CATEGORY === $recipesFilters->searchFormData?->field_filter) {
            $this->recipeSearchCriteria
                ->expects(self::once())
                ->method('addCategory')
                ->with(RECIPE_TYPE::from($recipesFilters->searchFormData->search_value));
        }

        if (FIELD_FILTERS::USER_NAME === $recipesFilters->searchFormData?->field_filter) {
            $this->recipeSearchCriteria
                ->expects(self::once())
                ->method('addUserName')
                ->with($recipesFilters->searchFormData->search_value, $recipesFilters->searchFormData->name_filter);
        }
    }

    /**
     * @return array<int, array{
     *  recipesFilters: RecipeFindServiceDto
     * }>
     */
    public static function recipeFindDataProvider(): iterable
    {
        $searchFormData = new SearchFormDataValidation();
        $searchFormData->field_filter = FIELD_FILTERS::NAME;
        $searchFormData->name_filter = SEARCH_TEXT_FILTER::EQUALS;
        $searchFormData->search_value = 'search value';

        yield [
            'recipesFilters' => new RecipeFindServiceDto(
                'user id',
                'group id',
                true,
                1,
                10,
                $searchFormData
            )];

        $searchFormData = new SearchFormDataValidation();
        $searchFormData->field_filter = FIELD_FILTERS::CATEGORY;
        $searchFormData->name_filter = SEARCH_TEXT_FILTER::EQUALS;
        $searchFormData->search_value = RECIPE_TYPE::DESSERT->value;

        yield [
            'recipesFilters' => new RecipeFindServiceDto(
                'user id',
                'group id',
                true,
                2,
                20,
                $searchFormData
            )];

        $searchFormData = new SearchFormDataValidation();
        $searchFormData->field_filter = FIELD_FILTERS::USER_NAME;
        $searchFormData->name_filter = SEARCH_TEXT_FILTER::EQUALS;
        $searchFormData->search_value = 'user name';

        yield [
            'recipesFilters' => new RecipeFindServiceDto(
                'user id',
                'group id',
                false,
                3,
                30,
                $searchFormData
            )];

        yield [
            'recipesFilters' => new RecipeFindServiceDto(
                'user id',
                'group id',
                false,
                3,
                30,
                null
            )];
    }

    #[Test]
    #[DataProvider('recipeFindDataProvider')]
    public function itShouldFindRecipes(RecipeFindServiceDto $recipesFilters): void
    {
        $recipesExpected = new ArrayCollection([]);

        $this->MockCreateCriteriaSearch($recipesFilters);

        $this->recipeRepository
            ->expects(self::once())
            ->method('findCriteria')
            ->with($this->recipeSearchCriteria->criteria)
            ->willReturn($recipesExpected);

        $return = $this->object->__invoke($recipesFilters);

        self::assertEquals($recipesExpected, $return);
    }

    #[Test]
    public function itShouldFailFindIngRecipesNotFound(): void
    {
        $recipesFilters = new RecipeFindServiceDto(
            'user id',
            'group id',
            false,
            3,
            30,
            null
        );

        $this->MockCreateCriteriaSearch($recipesFilters);

        $this->recipeRepository
            ->expects(self::once())
            ->method('findCriteria')
            ->with($this->recipeSearchCriteria->criteria)
            ->willThrowException(DBNotFoundException::fromMessage('Not found'));

        $return = $this->object->__invoke($recipesFilters);
        self::assertEmpty($return);
    }
}
