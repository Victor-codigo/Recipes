<?php

declare(strict_types=1);

namespace App\Service\Recipe\RecipeModify;

use App\Form\Recipe\RecipeModify\RecipeModifyFormDataMapper;
use App\Form\Recipe\RecipeModify\RecipeModifyFormDataValidation;
use App\Repository\RecipeRepository;
use App\Service\Exception\RecipeModifyException;

class RecipeModifyService
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private RecipeModifyFormDataMapper $recipeModifyFormDataMapper,
    ) {
    }

    /**
     * @throws RecipeModifyException
     */
    public function __invoke(RecipeModifyFormDataValidation $formData): void
    {
        try {
            $recipe = $this->recipeRepository->findRecipeByIdOrFail($formData->id);
            $this->recipeModifyFormDataMapper->mergeToEntity($recipe, $formData);
            $this->recipeRepository->save($recipe);
        } catch (\Throwable $e) {
            throw new RecipeModifyException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }
}
