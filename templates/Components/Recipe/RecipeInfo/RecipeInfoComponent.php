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
    }
}
