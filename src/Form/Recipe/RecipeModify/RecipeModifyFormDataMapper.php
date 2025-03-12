<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeModify;

use App\Common\RECIPE_TYPE;
use App\Entity\Recipe;
use Symfony\Component\HttpFoundation\File\File;

class RecipeModifyFormDataMapper
{
    public function mergeToEntity(Recipe $recipeToModify, RecipeModifyFormDataValidation $recipeModifyFormConstraints): void
    {
        $category = RECIPE_TYPE::NO_CATEGORY;
        if (RECIPE_TYPE::NO_CATEGORY !== $recipeModifyFormConstraints->category) {
            $category = $recipeModifyFormConstraints->category;
        }

        $recipeToModify->setName($recipeModifyFormConstraints->name);
        $recipeToModify->setCategory($category);
        $recipeToModify->setDescription($recipeModifyFormConstraints->description);
        $recipeToModify->setPreparationTime($recipeModifyFormConstraints->preparation_time);
        $recipeToModify->setIngredients($recipeModifyFormConstraints->ingredients);
        $recipeToModify->setSteps($recipeModifyFormConstraints->steps);
        $recipeToModify->setPublic($recipeModifyFormConstraints->public);

        if ($recipeModifyFormConstraints->image_remove) {
            $recipeToModify->setImage(null);
        } elseif (null !== $recipeModifyFormConstraints->image) {
            $recipeToModify->setImage($recipeModifyFormConstraints->image->getFilename());
        }
    }

    public function toForm(Recipe $recipe): RecipeModifyFormDataValidation
    {
        $imageFile = null;
        if (null !== $recipe->getImage()) {
            $imageFile = new File($recipe->getImage(), false);
        }

        $recipeModifyFormDataValidation = new RecipeModifyFormDataValidation();
        $recipeModifyFormDataValidation->id = $recipe->getId();
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
