<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\HomeList\ListItem;

use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;

abstract class HomeListItemComponent extends TwigComponent
{
    public HomeListItemComponentLangDto $lang;
    public HomeListItemComponentDto&TwigComponentDtoInterface $data;

    abstract public function mount(HomeListItemComponentDto $data): void;

    abstract public static function getComponentName(): string;
}
