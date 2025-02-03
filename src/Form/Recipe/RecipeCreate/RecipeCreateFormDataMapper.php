<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeCreate;

use App\Common\RECIPE_TYPE;
use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\File;

class RecipeCreateFormDataMapper
{
    public function toEntity(RecipeCreateFormDataValidation $recipeCreateFormConstraints, User $userSession, string $recipeId, ?string $groupId): Recipe
    {
        $category = null;
        if (RECIPE_TYPE::NO_CATEGORY !== $recipeCreateFormConstraints->category) {
            $category = $recipeCreateFormConstraints->category->value;
        }

        return new Recipe(
            $recipeId,
            $userSession,
            $groupId,
            $recipeCreateFormConstraints->name,
            $category,
            $recipeCreateFormConstraints->description,
            $recipeCreateFormConstraints->preparation_time,
            $recipeCreateFormConstraints->ingredients,
            $recipeCreateFormConstraints->steps,
            $recipeCreateFormConstraints->image?->getFilename(),
            null,
            $recipeCreateFormConstraints->public
        );
    }

    public function toForm(Recipe $recipe): RecipeCreateFormDataValidation
    {
        $imageFile = null;
        if (null !== $recipe->getImage()) {
            $imageFile = new File($recipe->getImage(), false);
        }

        $recipeCreateFormDataValidation = new RecipeCreateFormDataValidation(
            $recipe->getName(),
            $recipe->getDescription(),
            $recipe->getIngredients(),
            $recipe->getSteps(),
            $imageFile,
            $recipe->getPreparationTime(),
            $recipe->getCategory(),
            $recipe->getPublic(),
        );

        return $recipeCreateFormDataValidation;
    }
}
