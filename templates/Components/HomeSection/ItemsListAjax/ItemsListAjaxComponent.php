<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\ItemsListAjax;

use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ItemsListAjaxComponent',
    template: 'Components/HomeSection/ItemsListAjax/ItemsListAjaxComponent.html.twig'
)]
abstract class ItemsListAjaxComponent extends TwigComponent
{
    abstract protected function loadTranslation(): void;

    public ItemsListAjaxComponentLangDto $lang;
    public ItemsListAjaxComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'ItemsListAjaxComponent';
    }

    public function mount(ItemsListAjaxComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();
        $this->titleDto = $this->createTitle();
    }

    private function createTitle(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::POP_UP, null);
    }

    public function getSection(): string
    {
        return $this->data->section->value;
    }
}
