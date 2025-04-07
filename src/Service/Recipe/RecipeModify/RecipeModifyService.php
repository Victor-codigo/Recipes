<?php

declare(strict_types=1);

namespace App\Service\Recipe\RecipeModify;

use App\Common\Image\ImageInterface;
use App\Entity\Recipe;
use App\Form\Recipe\RecipeModify\RECIPE_MODIFY_FORM_FIELDS;
use App\Form\Recipe\RecipeModify\RecipeModifyFormDataMapper;
use App\Form\Recipe\RecipeModify\RecipeModifyFormDataValidation;
use App\Repository\RecipeRepository;
use App\Service\Exception\RecipeModifyException;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use VictorCodigo\SymfonyFormExtended\Form\FormExtendedInterface;

class RecipeModifyService
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private RecipeModifyFormDataMapper $recipeModifyFormDataMapper,
        private Filesystem $filesystem,
        private ImageInterface $image,
        private string $appConfigRecipeUploadedPath,
        private readonly int $appConfigImageSaveSizeWidth,
        private readonly int $appConfigImageSaveSizeHeight,
    ) {
    }

    /**
     * @throws RecipeModifyException
     */
    public function __invoke(Request $request, FormExtendedInterface $form, ?string $groupId): void
    {
        /** @var RecipeModifyFormDataValidation */
        $formData = $form->getData();

        try {
            /** @var Recipe */
            $recipe = $this->recipeRepository
                ->findRecipesByIdAndGroupIdOrFail(new ArrayCollection([$formData->id]), $groupId)
                ->first();

            $this->uploadRecipeImage($request, $form, $recipe);
            $this->recipeModifyFormDataMapper->mergeToEntity($recipe, $formData);

            if (null !== $formData->image) {
                $this->image->resizeToAFrame(
                    $this->appConfigRecipeUploadedPath.'/'.$recipe->getImage(),
                    $this->appConfigImageSaveSizeWidth,
                    $this->appConfigImageSaveSizeHeight
                );
            }

            $this->recipeRepository->save($recipe);
        } catch (\Throwable $e) {
            throw RecipeModifyException::fromMessage($e->getMessage())->log();
        }
    }

    /**
     * @throws IOException
     */
    private function uploadRecipeImage(Request $request, FormExtendedInterface $form, Recipe $recipe): void
    {
        $recipeImageOldToRemove = null;
        if (null !== $recipe->getImage()) {
            $recipeImageOldToRemove = $recipe->getImage();
        }

        /** @var RecipeModifyFormDataValidation */
        $formData = $form->getData();

        if ($formData->{RECIPE_MODIFY_FORM_FIELDS::IMAGE_REMOVE->value} && null !== $recipeImageOldToRemove) {
            $this->filesystem->remove("{$this->appConfigRecipeUploadedPath}/{$recipeImageOldToRemove}");

            return;
        }

        $form->uploadFiles(
            $request,
            $this->appConfigRecipeUploadedPath,
            null === $recipeImageOldToRemove ? [] : [$recipeImageOldToRemove]
        );
    }
}
