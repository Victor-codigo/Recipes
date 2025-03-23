<?php

declare(strict_types=1);

namespace App\Service\Recipe\RecipeFind;

use App\Common\RECIPE_TYPE;
use App\Entity\Recipe;
use App\Form\HomeSection\SearchBar\FIELD_FILTERS;
use App\Repository\RecipeRepository;
use App\Repository\RecipeSearchCriteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class RecipeFindService
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private RecipeSearchCriteria $recipeSearchCriteria,
    ) {
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function __invoke(RecipeFindServiceDto $filters): Collection
    {
        try {
            $searchCriteria = $this->createSearchCriteria($filters);
            $recipes = $this->recipeRepository->findCriteria($searchCriteria->criteria);

            return $recipes;
        } catch (\Throwable $th) {
            return new ArrayCollection([]);
        }
    }

    private function createSearchCriteria(RecipeFindServiceDto $filters): RecipeSearchCriteria
    {
        $this->recipeSearchCriteria
            ->addUserId($filters->userId)
            ->addGroupId($filters->groupId)
            ->addPublic($filters->public)
            ->setPagination($filters->page, $filters->pageItems)
            ->setOrderByRecipeName();

        if (null === $filters->searchFormData) {
            return $this->recipeSearchCriteria;
        }

        match ($filters->searchFormData->field_filter) {
            FIELD_FILTERS::NAME => $this->recipeSearchCriteria->addName($filters->searchFormData->search_value, $filters->searchFormData->name_filter),
            FIELD_FILTERS::CATEGORY => $this->recipeSearchCriteria->addCategory(RECIPE_TYPE::from($filters->searchFormData->search_value)),
            FIELD_FILTERS::USER_NAME => $this->recipeSearchCriteria->addUserName($filters->searchFormData->search_value, $filters->searchFormData->name_filter),
        };

        return $this->recipeSearchCriteria;
    }
}
