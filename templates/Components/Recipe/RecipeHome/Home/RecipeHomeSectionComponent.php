<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeHome\Home;

use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'RecipeHomeSectionComponent',
    template: 'Components/Recipe/RecipeHome/Home/RecipeHomeSectionComponent.html.twig'
)]
class RecipeHomeSectionComponent extends TwigComponent
{
    public RecipeHomeSectionComponentDto&TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'RecipeHomeSectionComponent';
    }

    public function mount(RecipeHomeSectionComponentDto $data): void
    {
        $this->data = $data;
    }
}
