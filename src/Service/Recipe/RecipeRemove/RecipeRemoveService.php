<?php

declare(strict_types=1);

namespace App\Service\Recipe\RecipeRemove;

use App\Entity\Recipe;
use App\Form\Recipe\RecipeRemove\RecipeRemoveFormDataMapper;
use App\Form\Recipe\RecipeRemove\RecipeRemoveFormDataValidation;
use App\Repository\RecipeRepository;
use App\Service\Exception\RecipeRemoveException;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use VictorCodigo\SymfonyFormExtended\Form\FormExtendedInterface;

class RecipeRemoveService
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private RecipeRemoveFormDataMapper $recipeRemoveFormDataMapper,
        private Filesystem $fileSystem,
        private readonly string $appConfigRecipeUploadedPath,
    ) {
    }

    /**
     * @throws RecipeRemoveException
     */
    public function __invoke(FormExtendedInterface $form, ?string $groupId): void
    {
        /** @var RecipeRemoveFormDataValidation */
        $formData = $form->getData();
        $recipesId = $this->recipeRemoveFormDataMapper->toRecipesIdCollection($formData);

        try {
            $recipesToRemove = $this->recipeRepository->findRecipesByIdAndGroupIdOrFail($recipesId, $groupId);
            $this->recipesRemoveImage($recipesToRemove);

            $this->recipeRepository->remove($recipesToRemove);
        } catch (\Throwable $th) {
            throw RecipeRemoveException::fromMessage($th->getMessage())->log();
        }
    }

    /**
     * @param Collection<int, Recipe> $recipes
     *
     * @throws IOException
     */
    private function recipesRemoveImage(Collection $recipes): void
    {
        foreach ($recipes as $recipe) {
            if (null === $recipe->getImage()) {
                continue;
            }

            $this->fileSystem->remove("{$this->appConfigRecipeUploadedPath}/{$recipe->getImage()}");
        }
    }
}
