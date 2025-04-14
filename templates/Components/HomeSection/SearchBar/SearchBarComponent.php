<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\SearchBar;

use App\Common\RECIPE_TYPE;
use App\Form\HomeSection\SearchBar\FIELD_FILTERS;
use App\Form\HomeSection\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'SearchBarComponent',
    template: 'Components/HomeSection/SearchBar/SearchBarComponent.html.twig'
)]
class SearchBarComponent extends TwigComponent
{
    public SearchBarComponentDto&TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $searchTokenCsrfFieldName;

    public readonly string $searchValueFieldName;
    public readonly string $nameFilterFieldName;
    public readonly string $fieldFilterFieldName;
    public readonly string $searchButtonFiledName;
    /**
     * @var array<string, string>
     */
    public readonly array $nameFilters;
    /**
     * @var array<string, string>
     */
    public readonly array $fieldFilters;
    /**
     * @var array<string, string>
     */
    public readonly array $categoryFilters;

    protected static function getComponentName(): string
    {
        return 'SearchBarComponent';
    }

    public function mount(SearchBarComponentDto&TwigComponentDtoInterface $data): void
    {
        $this->data = $data;
        $this->formName = SEARCHBAR_FORM_FIELDS::FORM_NAME->value;
        $this->searchTokenCsrfFieldName = SEARCHBAR_FORM_FIELDS::getNameWithForm(SEARCHBAR_FORM_FIELDS::CSRF_TOKEN);
        $this->fieldFilterFieldName = SEARCHBAR_FORM_FIELDS::getNameWithForm(SEARCHBAR_FORM_FIELDS::FIELD_FILTER);
        $this->nameFilterFieldName = SEARCHBAR_FORM_FIELDS::getNameWithForm(SEARCHBAR_FORM_FIELDS::NAME_FILTER);
        $this->searchValueFieldName = SEARCHBAR_FORM_FIELDS::getNameWithForm(SEARCHBAR_FORM_FIELDS::SEARCH_VALUE);
        $this->searchButtonFiledName = SEARCHBAR_FORM_FIELDS::getNameWithForm(SEARCHBAR_FORM_FIELDS::SUBMIT);

        $this->nameFilters = [
            NAME_FILTERS::STARTS_WITH->value => $this->translate('name_filters.startsWith'),
            NAME_FILTERS::ENDS_WITH->value => $this->translate('name_filters.endsWith'),
            NAME_FILTERS::CONTAINS->value => $this->translate('name_filters.contains'),
            NAME_FILTERS::EQUALS->value => $this->translate('name_filters.equals'),
        ];

        $this->fieldFilters = [
            FIELD_FILTERS::NAME->value => $this->translate('field_filters.name'),
            FIELD_FILTERS::CATEGORY->value => $this->translate('field_filters.category'),
            FIELD_FILTERS::USER_NAME->value => $this->translate('field_filters.user_name'),
        ];

        $this->categoryFilters = [
            RECIPE_TYPE::NO_CATEGORY->value => $this->translate('category_filters.no_category'),
            RECIPE_TYPE::BREAKFAST->value => $this->translate('category_filters.breakfast'),
            RECIPE_TYPE::BRUNCH->value => $this->translate('category_filters.brunch'),
            RECIPE_TYPE::LUNCH->value => $this->translate('category_filters.lunch'),
            RECIPE_TYPE::DINNER->value => $this->translate('category_filters.dinner'),
            RECIPE_TYPE::DESSERT->value => $this->translate('category_filters.dessert'),
            RECIPE_TYPE::SANDWICH->value => $this->translate('category_filters.sandwich'),
            RECIPE_TYPE::APPETISER->value => $this->translate('category_filters.appetiser'),
            RECIPE_TYPE::SOUP->value => $this->translate('category_filters.soup'),
            RECIPE_TYPE::SALAD->value => $this->translate('category_filters.salad'),
            RECIPE_TYPE::SNACK->value => $this->translate('category_filters.snack'),
            RECIPE_TYPE::BURGER->value => $this->translate('category_filters.burger'),
            RECIPE_TYPE::PIZZA->value => $this->translate('category_filters.pizza'),
            RECIPE_TYPE::CAKE->value => $this->translate('category_filters.cake'),
            RECIPE_TYPE::SEAFOOD->value => $this->translate('category_filters.seafood'),
            RECIPE_TYPE::RICE->value => $this->translate('category_filters.rice'),
            RECIPE_TYPE::PASTA->value => $this->translate('category_filters.pasta'),
            RECIPE_TYPE::ICE_CREAM->value => $this->translate('category_filters.ice_cream'),
            RECIPE_TYPE::MEAT->value => $this->translate('category_filters.meat'),
        ];
    }

    public function sectionFilters(): array
    {
        $sectionFiltersKeys = array_keys($this->fieldFilters);
        $sectionFilters = array_map(
            fn (string $sectionFilter, string $key) => in_array(FIELD_FILTERS::tryFrom($key), $this->data->sectionFilters) ? $sectionFilter : null,
            array_values($this->fieldFilters), $sectionFiltersKeys
        );

        $sectionFilters = array_combine($sectionFiltersKeys, $sectionFilters);

        return array_filter($sectionFilters);
    }

    public function getSearchValue(): string
    {
        if ($this->data->fieldFilterValue === FIELD_FILTERS::NAME->value || $this->data->fieldFilterValue === FIELD_FILTERS::USER_NAME->value) {
            return $this->data->searchValue;
        }

        return $this->categoryFilters[$this->data->searchValue] ?? '';
    }
}
