<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeHome\ListItem;

use App\Templates\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Templates\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'RecipeListItemComponent',
    template: 'Components/RecipesHome/ListItem/RecipeListItemComponent.html.twig'
)]
final class RecipeListItemComponent extends HomeListItemComponent
{
    public readonly string $productDataJson;

    public static function getComponentName(): string
    {
        return 'RecipeListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
        // $this->productDataJson = $this->parseItemDataToJson($data);
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new RecipeListItemComponentLangDto(
            $this->translate('recipe_modify_button.label'),
            $this->translate('recipe_modify_button.alt'),
            $this->translate('recipe_modify_button.title'),
            $this->translate('recipe_remove_button.label'),
            $this->translate('recipe_remove_button.alt'),
            $this->translate('recipe_remove_button.title'),
            $this->translate('recipe_info_button.alt'),
            $this->translate('recipe_info_button.title'),
            $this->translate('recipe_image.alt'),
            $this->translate('recipe_image.title'),
        );
    }

    // private function parseItemDataToJson(RecipeListItemComponentDto $recipeData): string
    // {
    //     /** @var ProductRecipePriceDataResponse[] $productRecipesPricesDataByProductId */
    //     $productRecipesPricesDataByProductId = array_combine(
    //         array_map(
    //             fn (ProductRecipePriceDataResponse $productRecipePrice) => $productRecipePrice->productId,
    //             $recipeData->productsRecipesPrice
    //         ),
    //         $recipeData->productsRecipesPrice
    //     );

    //     $productRecipesData = array_map(
    //         fn (ProductDataResponse $productData) => [
    //             'id' => $productData->id,
    //             'name' => $productData->name,
    //             'description' => $productData->description,
    //             'image' => $productData->image,
    //             'price' => $productRecipesPricesDataByProductId[$productData->id]->price,
    //             'unit' => $productRecipesPricesDataByProductId[$productData->id]->unitMeasure,
    //         ],
    //         $recipeData->products
    //     );

    //     $recipeDataToParse = [
    //         'id' => $recipeData->id,
    //         'name' => $recipeData->name,
    //         'address' => $recipeData->address,
    //         'description' => $recipeData->description,
    //         'image' => $recipeData->image,
    //         'noImage' => $recipeData->noImage,
    //         'createdOn' => $recipeData->createdOn->format('Y-m-d'),
    //         'itemsPrices' => $productRecipesData,
    //     ];

    //     return json_encode($recipeDataToParse, JSON_THROW_ON_ERROR);
    // }
}
