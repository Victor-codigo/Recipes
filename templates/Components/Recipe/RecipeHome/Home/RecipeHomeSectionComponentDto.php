<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeHome\Home;

use App\Common\DtoBuilder\DtoBuilder;
use App\Common\DtoBuilder\DtoBuilderInterface;
use App\Templates\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Templates\Components\Modal\ModalComponentDto;
use App\Templates\Components\TwigComponentDtoInterface;

class RecipeHomeSectionComponentDto implements TwigComponentDtoInterface, DtoBuilderInterface
{
    private DtoBuilder $builder;

    public readonly HomeSectionComponentDto $homeSectionComponentDto;
    public readonly ModalComponentDto $listItemsModalDto;
    public readonly ModalComponentDto $recipeInfoModalDto;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'homeSection',
            'recipeInfoModal',
        ]);
    }

    public function homeSection(HomeSectionComponentDto $homeSectionComponentDto): self
    {
        $this->builder->setMethodStatus('homeSection', true);

        $this->homeSectionComponentDto = $homeSectionComponentDto;

        return $this;
    }

    public function recipeInfoModal(ModalComponentDto $recipeInfoModalDto): self
    {
        $this->builder->setMethodStatus('recipeInfoModal', true);

        $this->recipeInfoModalDto = $recipeInfoModalDto;

        return $this;
    }

    public function build(): DtoBuilderInterface
    {
        $this->builder->validate();

        return $this;
    }
}
