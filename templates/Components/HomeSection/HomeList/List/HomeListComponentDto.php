<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\HomeList\List;

use App\Templates\Components\Modal\ModalComponentDto;
use App\Templates\Components\Paginator\PaginatorComponentDto;
use App\Templates\Components\TwigComponentDtoInterface;

class HomeListComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param HomeListItemComponentDto[] $listItems
     */
    public function __construct(
        public readonly array $errors,
        public readonly string $listItemComponentName,
        public readonly array $listItems,
        public readonly PaginatorComponentDto $homeListPaginatorDto,
        public readonly bool $validForm,
        public readonly ModalComponentDto $homeListItemRemoveFormModalDto,
        public readonly ?ModalComponentDto $homeListItemModifyFormModalDto,

        public readonly string $translationDomainName,
    ) {
    }
}
