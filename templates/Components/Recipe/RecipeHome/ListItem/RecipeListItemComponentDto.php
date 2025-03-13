<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeHome\ListItem;

use App\Common\RECIPE_TYPE;
use App\Templates\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;

readonly class RecipeListItemComponentDto extends HomeListItemComponentDto
{
    /**
     * @param array<int, string> $ingredients
     * @param array<int, string> $steps
     */
    public function __construct(
        public string $componentName,
        public string $id,
        public string $userOwnerName,
        public string $name,
        public ?string $description,
        public ?\DateTimeImmutable $preparationTime,
        public RECIPE_TYPE $category,
        public bool $public,
        public array $ingredients,
        public array $steps,
        public ?string $image,
        public ?int $rating,
        // public string $recipeData,
        public string $modifyFormModalIdAttribute,
        public string $deleteFormModalIdAttribute,
        public string $infoFormModalIdAttribute,
        public string $translationDomainName,
    ) {
    }
}
