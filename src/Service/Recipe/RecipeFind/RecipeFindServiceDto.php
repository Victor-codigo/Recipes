<?php

declare(strict_types=1);

namespace App\Service\Recipe\RecipeFind;

use App\Form\HomeSection\SearchBar\SearchFormDataValidation;

readonly class RecipeFindServiceDto
{
    public function __construct(
        public ?string $userId,
        public ?string $groupId,
        public ?bool $public,
        public int $page,
        public int $pageItems,
        public ?SearchFormDataValidation $searchFormData,
    ) {
    }
}
