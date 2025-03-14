<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\ItemRemove;

use App\Templates\Components\Title\TITLE_TYPE;
use App\Templates\Components\Title\TitleComponentDto;
use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ItemRemoveComponent',
    template: 'Components/HomeSection/ItemRemove/ItemRemoveComponent.html.twig'
)]
abstract class ItemRemoveComponent extends TwigComponent
{
    abstract public function mount(ItemRemoveComponentDto $data): void;

    abstract public static function getComponentName(): string;

    public ItemRemoveComponentDto&TwigComponentDtoInterface $data;
    public TitleComponentDto $titleDto;

    public readonly string $componentName;
    public readonly string $title;
    public readonly string $formName;
    public readonly string $submitFieldName;
    public readonly string $itemsIdFieldName;
    public readonly string $tokenCsrfFieldName;

    public readonly string $messageAdvice;
    public readonly string $itemCloseButtonLabel;
    public readonly string $itemRemoveButton;

    protected function initialize(
        string $componentName,
        string $title,
        string $formName,
        string $submitFieldName,
        string $itemsIdFieldName,
        string $tokenCsrfFieldName,
        string $messageAdvice,
        string $itemCloseButtonLabel,
        string $itemRemoveButton,
    ): void {
        $this->componentName = $componentName;
        $this->title = $title;
        $this->formName = $formName;
        $this->submitFieldName = $submitFieldName;
        $this->itemsIdFieldName = $itemsIdFieldName;
        $this->tokenCsrfFieldName = $tokenCsrfFieldName;
        $this->messageAdvice = $messageAdvice;
        $this->itemCloseButtonLabel = $itemCloseButtonLabel;
        $this->itemRemoveButton = $itemRemoveButton;
    }

    protected function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->title, TITLE_TYPE::POP_UP, null);
    }
}
