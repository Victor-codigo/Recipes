<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeInfo;

use App\Templates\Components\TwigComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'RecipeInfoComponent',
    template: 'Components/Recipe/RecipeInfo/RecipeInfoComponent.html.twig'
)]
class RecipeInfoComponent extends TwigComponent
{
    public RecipeInfoComponentDto $data;

    public static function getComponentName(): string
    {
        return 'RecipeInfoComponent';
    }

    public function mount(RecipeInfoComponentDto $data): void
    {
        $this->data = $data;
        // $this->loadTranslation();
    }

    // protected function loadTranslation(): void
    // {
    //     $this->lang = (new ProductInfoComponentLangDto())
    //         ->info(
    //             $this->translate('image.title'),
    //             $this->translate('image.alt'),
    //             $this->translate('created_on'),
    //             null
    //         )
    //         ->description(
    //             $this->translate('description.label'),
    //         )
    //         ->priceHeaders(
    //             $this->translate('item_price.name'),
    //             $this->translate('item_price.price'),
    //             $this->translate('item_price.unit'),
    //         )
    //         ->shopsEmpty(
    //             $this->translate('shops.empty')
    //         )
    //         ->buttons(
    //             $this->translate('close_button.label')
    //         )
    //         ->build();
    // }
}
