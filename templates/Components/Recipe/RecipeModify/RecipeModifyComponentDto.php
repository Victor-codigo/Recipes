<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeModify;

use App\Templates\Components\TwigComponentDtoInterface;

readonly class RecipeModifyComponentDto implements TwigComponentDtoInterface
{
    public ?string $csrfToken;
    public string $recipeModifyFormActionUrl;

    public string $id;
    public string $name;
    public ?string $description;
    public array $steps;
    public array $ingredients;
    public ?string $image;
    public ?\DateTimeImmutable $preparationTime;
    public ?string $category;
    public bool $public;

    public function form(?string $csrfToken, string $recipeModifyFormActionUrl): self
    {
        $this->csrfToken = $csrfToken;
        $this->recipeModifyFormActionUrl = $recipeModifyFormActionUrl;

        return $this;
    }

    public function formFields(
        string $id,
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
