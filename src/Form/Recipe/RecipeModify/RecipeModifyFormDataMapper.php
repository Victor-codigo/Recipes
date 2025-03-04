<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeModify;

use App\Common\RECIPE_TYPE;
use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\File;

class RecipeModifyFormDataMapper
{
    public function toEntity(RecipeModifyFormDataValidation $recipeModifyFormConstraints, User $userSession, string $recipeId, ?string $groupId): Recipe
    {
        $category = null;
        if (RECIPE_TYPE::NO_CATEGORY !== $recipeModifyFormConstraints->category) {
            $category = $recipeModifyFormConstraints->category->value;
        }

        return new Recipe(
            $recipeId,
            $userSession,
            $groupId,
            $recipeModifyFormConstraints->name,
            $category,
            $recipeModifyFormConstraints->description,
            $recipeModifyFormConstraints->preparation_time,
            $recipeModifyFormConstraints->ingredients,
            $recipeModifyFormConstraints->steps,
            $recipeModifyFormConstraints->image?->getFilename(),
            null,
            $recipeModifyFormConstraints->public
        );
    }

    public function toForm(Recipe $recipe): RecipeModifyFormDataValidation
    {
        $imageFile = null;
        if (null !== $recipe->getImage()) {
            $imageFile = new File($recipe->getImage(), false);
        }

        $recipeModifyFormDataValidation = new RecipeModifyFormDataValidation();
        $recipeModifyFormDataValidation->name = $recipe->getName();
        $recipeModifyFormDataValidation->description = $recipe->getDescription();
        $recipeModifyFormDataValidation->ingredients = $recipe->getIngredients();
        $recipeModifyFormDataValidation->steps = $recipe->getSteps();
        $recipeModifyFormDataValidation->image = $imageFile;
        $recipeModifyFormDataValidation->preparation_time = $recipe->getPreparationTime();
        $recipeModifyFormDataValidation->category = $recipe->getCategory();
        $recipeModifyFormDataValidation->public = $recipe->getPublic();

        return $recipeModifyFormDataValidation;
    }
}
