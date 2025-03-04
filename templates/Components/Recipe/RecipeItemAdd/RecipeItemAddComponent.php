<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeItemAdd;

use App\Templates\Components\TwigComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'RecipeItemAddComponent',
    template: 'Components/Recipe/RecipeItemAdd/RecipeItemAddComponent.html.twig'
)]
class RecipeItemAddComponent extends TwigComponent
{
    public readonly RecipeItemAddComponentDto $data;

    public static function getComponentName(): string
    {
        return 'RecipeItemAddComponent';
    }

    public function mount(RecipeItemAddComponentDto $data): void
    {
        $this->data = $data;
    }
}
