<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeHome\ListItem;

use App\Templates\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Templates\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'RecipeListItemComponent',
    template: 'Components/Recipe/RecipeHome/ListItem/RecipeListItemComponent.html.twig'
)]
final class RecipeListItemComponent extends HomeListItemComponent
{
    public readonly string $recipeDataJson;

    public static function getComponentName(): string
    {
        return 'RecipeListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->recipeDataJson = $this->parseItemDataToJson($data);
    }

    /**
     * @throws \JsonException
     */
    private function parseItemDataToJson(RecipeListItemComponentDto $recipeData): string
    {
        $recipeDataToParse = [
            'userOwnerName' => $recipeData->userOwnerName,
            'id' => $recipeData->id,
            'name' => $recipeData->name,
            'description' => $recipeData->description,
            'preparation_time' => $recipeData->preparationTime?->format('H:i'),
            'category' => $recipeData->category->value,
            'public' => $recipeData->public,
            'ingredients' => $recipeData->ingredients,
            'steps' => $recipeData->steps,
            'image' => $recipeData->image,
            'rating' => $recipeData->rating,
        ];

        return json_encode($recipeDataToParse, JSON_THROW_ON_ERROR);
    }
}
