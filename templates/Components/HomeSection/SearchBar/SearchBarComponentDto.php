<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\SearchBar;

use App\Templates\Components\TwigComponentDtoInterface;

class SearchBarComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param SECTION_FILTERS[] $sectionFilters
     */
    public function __construct(
        public readonly string $groupId,
        public readonly ?string $searchValue,
        public readonly array $sectionFilters,
        public readonly ?string $sectionFilterValue,
        public readonly ?string $nameFilterValue,
        public readonly string $searchCsrfToken,
        public readonly string $searchFormActionUrl,
        public readonly string $searchAutoCompleteUrl,
    ) {
    }
}
