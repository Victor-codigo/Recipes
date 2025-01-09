<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\ItemsListAjax;

use App\Twig\Components\Controls\PaginatorContentLoaderJs\PaginatorContentLoaderJsComponentDto;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use App\Twig\Components\TwigComponentDtoInterface;

class ItemsListAjaxComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $componentName,
        public readonly string $groupId,
        public readonly SECTION_FILTERS $section,
        public readonly PaginatorContentLoaderJsComponentDto $paginatorContentLoaderJsDto,
        public readonly string $urlPathItemsImages,
        public readonly string $urlNoItemsImage,
        public readonly string $urlListEmptyImage,
    ) {
    }
}
