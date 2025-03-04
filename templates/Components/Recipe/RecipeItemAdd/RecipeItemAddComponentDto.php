<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeItemAdd;

use App\Templates\Components\TwigComponentDtoInterface;

readonly class RecipeItemAddComponentDto implements TwigComponentDtoInterface
{
    public string $componentSelector;
    public string $componentErrorMsg;
    public string $componentLabel;

    public string $itemNameField;
    public string $itemLabel;
    public string $itemPlaceholder;
    public string $itemErrorMsg;
    public TYPE_INPUT $inputType;

    public string $buttonItemAddLabel;
    public string $buttonItemAddTitle;

    public function component(string $componentSelector, string $componentLabel, string $componentErrorMsg): self
    {
        $this->componentSelector = $componentSelector;
        $this->componentLabel = $componentLabel;
        $this->componentErrorMsg = $componentErrorMsg;

        return $this;
    }

    public function items(TYPE_INPUT $inputType, string $itemNameField, string $itemLabel, string $itemPlaceholder, string $itemErrorMsg): self
    {
        $this->inputType = $inputType;
        $this->itemNameField = $itemNameField;
        $this->itemLabel = $itemLabel;
        $this->itemPlaceholder = $itemPlaceholder;
        $this->itemErrorMsg = $itemErrorMsg;

        return $this;
    }

    public function buttonAdd(string $buttonItemAddLabel, string $buttonItemAddTitle): self
    {
        $this->buttonItemAddLabel = $buttonItemAddLabel;
        $this->buttonItemAddTitle = $buttonItemAddTitle;

        return $this;
    }
}
