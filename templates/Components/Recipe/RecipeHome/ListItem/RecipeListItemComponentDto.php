<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeHome\ListItem;

use App\Templates\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;

readonly class RecipeListItemComponentDto extends HomeListItemComponentDto
{
    public function __construct(
        public string $componentName,
        public string $id,
        public string $userOwnerName,
        public string $name,
        public ?string $category,
        public ?string $image,
        public ?int $rating,
        public string $recipeData,
        public string $modifyFormModalIdAttribute,
        public string $deleteFormModalIdAttribute,
        public string $infoFormModalIdAttribute,
    ) {
    }
}
