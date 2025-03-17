<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeRemove;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class RecipeRemoveFormDataMapper
{
    /**
     * @return Collection<int, string>
     */
    public function toRecipesIdCollection(RecipeRemoveFormDataValidation $recipeRemoveFormConstraints): Collection
    {
        return new ArrayCollection($recipeRemoveFormConstraints->recipes_id);
    }
}
