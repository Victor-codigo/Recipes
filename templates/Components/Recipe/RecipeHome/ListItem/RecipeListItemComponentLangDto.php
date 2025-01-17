<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeHome\ListItem;

use App\Templates\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;

class RecipeListItemComponentLangDto extends HomeListItemComponentLangDto
{
    public function __construct(
        public readonly string $modifyItemButtonLabel,
        public readonly string $modifyItemButtonAlt,
        public readonly string $modifyItemButtonTitle,
        public readonly string $removeItemButtonLabel,
        public readonly string $removeItemButtonAlt,
        public readonly string $removeItemButtonTitle,
        public readonly string $infoItemButtonAlt,
        public readonly string $infoItemButtonTitle,

        public readonly string $imageItemAlt,
        public readonly string $imageItemTitle,
    ) {
    }
}
