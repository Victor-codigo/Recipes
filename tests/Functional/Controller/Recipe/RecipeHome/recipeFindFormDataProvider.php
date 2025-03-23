<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Recipe\RecipeHome;

use App\Common\RECIPE_TYPE;
use App\Form\HomeSection\SearchBar\FIELD_FILTERS;
use App\Form\HomeSection\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Form\HomeSection\SearchBar\SEARCH_TEXT_FILTER;

class recipeFindFormDataProvider
{
    /**
     * @return iterable<array{
     *  request: array{
     *    field_filter: string,
     *    name_filter?: string,
     *    search_value: string,
     *  },
     *  recipesFoundExpected: array<int, string>,
     * }>
     */
    public static function dataProvider(): iterable
    {
        yield self::formWithNameEqualTo();
        yield self::formWithNameStartsWith();
        yield self::formWithNameEndsWith();
        yield self::formWithNameContains();

        yield self::formWithCategoryDinner();
        yield self::formWithCategoryNoCategory();

        yield self::formWithUserNameEquals();
        yield self::formWithUserNameStartsWith();
        yield self::formWithUserNameEndsWith();
        yield self::formWithUserNameContains();
    }

    /**
     * @return array{
     *  request: array{
     *    field_filter: string,
     *    name_filter: string,
     *    search_value: string,
     *  },
     *  recipesFoundExpected: array<int, string>,
     * }
     */
    private static function formWithNameEqualTo(): array
    {
        return [
            'request' => [
                SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => FIELD_FILTERS::NAME->value,
                SEARCHBAR_FORM_FIELDS::NAME_FILTER->value => SEARCH_TEXT_FILTER::EQUALS->value,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => 'recipe_6 name',
            ],
            'recipesFoundExpected' => [
                'recipe_6 name',
            ],
        ];
    }

    /**
     * @return array{
     *  request: array{
     *    field_filter: string,
     *    name_filter: string,
     *    search_value: string,
     *  },
     *  recipesFoundExpected: array<int, string>,
     * }
     */
    private static function formWithNameStartsWith(): array
    {
        return [
            'request' => [
                SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => FIELD_FILTERS::NAME->value,
                SEARCHBAR_FORM_FIELDS::NAME_FILTER->value => SEARCH_TEXT_FILTER::STARTS_WITH->value,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => 'recipe',
            ],
            'recipesFoundExpected' => [
                'recipe_1 name',
                'recipe_4 name',
                'recipe_5 name',
                'recipe_6 name',
                'recipe_7 name',
            ],
        ];
    }

    /**
     * @return array{
     *  request: array{
     *    field_filter: string,
     *    name_filter: string,
     *    search_value: string,
     *  },
     *  recipesFoundExpected: array<int, string>,
     * }
     */
    private static function formWithNameEndsWith(): array
    {
        return [
            'request' => [
                SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => FIELD_FILTERS::NAME->value,
                SEARCHBAR_FORM_FIELDS::NAME_FILTER->value => SEARCH_TEXT_FILTER::ENDS_WITH->value,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => 'name',
            ],
            'recipesFoundExpected' => [
                'recipe_1 name',
                'recipe_4 name',
                'recipe_5 name',
                'recipe_6 name',
                'recipe_7 name',
            ],
        ];
    }

    /**
     * @return array{
     *  request: array{
     *    field_filter: string,
     *    name_filter: string,
     *    search_value: string,
     *  },
     *  recipesFoundExpected: array<int, string>,
     * }
     */
    private static function formWithNameContains(): array
    {
        return [
            'request' => [
                SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => FIELD_FILTERS::NAME->value,
                SEARCHBAR_FORM_FIELDS::NAME_FILTER->value => SEARCH_TEXT_FILTER::CONTAINS->value,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => 'ipe_4',
            ],
            'recipesFoundExpected' => [
                'recipe_4 name',
            ],
        ];
    }

    /**
     * @return array{
     *  request: array{
     *    field_filter: string,
     *    search_value: string,
     *  },
     *  recipesFoundExpected: array<int, string>,
     * }
     */
    private static function formWithCategoryDinner(): array
    {
        return [
            'request' => [
                SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => FIELD_FILTERS::CATEGORY->value,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => RECIPE_TYPE::DINNER->value,
            ],
            'recipesFoundExpected' => [
                'recipe_4 name',
                'recipe_5 name',
                'recipe_6 name',
            ],
        ];
    }

    /**
     * @return array{
     *  request: array{
     *    field_filter: string,
     *    search_value: string,
     *  },
     *  recipesFoundExpected: array<int, string>,
     * }
     */
    private static function formWithCategoryNoCategory(): array
    {
        return [
            'request' => [
                SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => FIELD_FILTERS::CATEGORY->value,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => RECIPE_TYPE::NO_CATEGORY->value,
            ],
            'recipesFoundExpected' => [
                'recipe_7 name',
            ],
        ];
    }

    /**
     * @return array{
     *  request: array{
     *    field_filter: string,
     *    name_filter: string,
     *    search_value: string,
     *  },
     *  recipesFoundExpected: array<int, string>,
     * }
     */
    private static function formWithUserNameEquals(): array
    {
        return [
            'request' => [
                SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => FIELD_FILTERS::USER_NAME->value,
                SEARCHBAR_FORM_FIELDS::NAME_FILTER->value => SEARCH_TEXT_FILTER::EQUALS->value,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => 'user_1 name',
            ],
            'recipesFoundExpected' => [
                'recipe_1 name',
                'recipe_4 name',
                'recipe_5 name',
                'recipe_6 name',
                'recipe_7 name',
            ],
        ];
    }

    /**
     * @return array{
     *  request: array{
     *    field_filter: string,
     *    name_filter: string,
     *    search_value: string,
     *  },
     *  recipesFoundExpected: array<int, string>,
     * }
     */
    private static function formWithUserNameStartsWith(): array
    {
        return [
            'request' => [
                SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => FIELD_FILTERS::USER_NAME->value,
                SEARCHBAR_FORM_FIELDS::NAME_FILTER->value => SEARCH_TEXT_FILTER::STARTS_WITH->value,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => 'user_1',
            ],
            'recipesFoundExpected' => [
                'recipe_1 name',
                'recipe_4 name',
                'recipe_5 name',
                'recipe_6 name',
                'recipe_7 name',
            ],
        ];
    }

    /**
     * @return array{
     *  request: array{
     *    field_filter: string,
     *    name_filter: string,
     *    search_value: string,
     *  },
     *  recipesFoundExpected: array<int, string>,
     * }
     */
    private static function formWithUserNameEndsWith(): array
    {
        return [
            'request' => [
                SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => FIELD_FILTERS::USER_NAME->value,
                SEARCHBAR_FORM_FIELDS::NAME_FILTER->value => SEARCH_TEXT_FILTER::ENDS_WITH->value,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => '1 name',
            ],
            'recipesFoundExpected' => [
                'recipe_1 name',
                'recipe_4 name',
                'recipe_5 name',
                'recipe_6 name',
                'recipe_7 name',
            ],
        ];
    }

    /**
     * @return array{
     *  request: array{
     *    field_filter: string,
     *    name_filter: string,
     *    search_value: string,
     *  },
     *  recipesFoundExpected: array<int, string>,
     * }
     */
    private static function formWithUserNameContains(): array
    {
        return [
            'request' => [
                SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => FIELD_FILTERS::USER_NAME->value,
                SEARCHBAR_FORM_FIELDS::NAME_FILTER->value => SEARCH_TEXT_FILTER::CONTAINS->value,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => '1',
            ],
            'recipesFoundExpected' => [
                'recipe_1 name',
                'recipe_4 name',
                'recipe_5 name',
                'recipe_6 name',
                'recipe_7 name',
            ],
        ];
    }
}
