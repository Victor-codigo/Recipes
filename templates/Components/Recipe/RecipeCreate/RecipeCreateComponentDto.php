<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeCreate;

use App\Templates\Components\TwigComponentDtoInterface;

readonly class RecipeCreateComponentDto implements TwigComponentDtoInterface
{
    public ?string $csrfToken;
    public string $recipeCreateFormActionUrl;

    public string $name;
    public ?string $description;
    public array $steps;
    public array $ingredients;
    public ?string $image;
    public ?\DateTimeImmutable $preparationTime;
    public ?string $category;
    public bool $public;

    public function form(?string $csrfToken, string $recipeCreateFormActionUrl): self
    {
        $this->csrfToken = $csrfToken;
        $this->recipeCreateFormActionUrl = $recipeCreateFormActionUrl;

        return $this;
    }

    public function formFields(
        string $name,
        ?string $description,
        array $steps,
        array $ingredients,
        ?string $image,
        ?\DateTimeImmutable $preparationTime,
        ?string $category,
        bool $public,
    ): self {
        $this->name = $name;
        $this->description = $description;
        $this->steps = $steps;
        $this->ingredients = $ingredients;
        $this->image = $image;
        $this->preparationTime = $preparationTime;
        $this->category = $category;
        $this->public = $public;

        return $this;
    }
}
