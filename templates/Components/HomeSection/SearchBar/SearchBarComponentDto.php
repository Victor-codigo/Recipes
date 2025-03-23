<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\SearchBar;

use App\Form\HomeSection\SearchBar\FIELD_FILTERS;
use App\Templates\Components\TwigComponentDtoInterface;

class SearchBarComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param FIELD_FILTERS[] $sectionFilters
     */
    public function __construct(
        public readonly string $groupId,
        public readonly ?string $searchValue,
        public readonly array $sectionFilters,
        public readonly ?string $fieldFilterValue,
        public readonly ?string $nameFilterValue,
        public readonly string $searchCsrfToken,
        public readonly string $searchFormActionUrl,
        public readonly string $searchAutoCompleteUrl,
    ) {
    }
}
