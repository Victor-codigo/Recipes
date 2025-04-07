<?php

declare(strict_types=1);

namespace App\Service\Recipe\RecipeCreate;

use App\Common\Image\ImageInterface;
use App\Entity\User;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormDataMapper;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormDataValidation;
use App\Repository\RecipeRepository;
use App\Service\Exception\RecipeCreateException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use VictorCodigo\SymfonyFormExtended\Form\FormExtendedInterface;

class RecipeCreateService
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private Security $security,
        private RecipeCreateFormDataMapper $recipeCreateFormDataMapper,
        private ImageInterface $image,
        private readonly string $appConfigRecipeUploadedPath,
        private readonly int $appConfigImageSaveSizeWidth,
        private readonly int $appConfigImageSaveSizeHeight,
    ) {
    }

    /**
     * @throws RecipeCreateException
     */
    public function __invoke(Request $request, FormExtendedInterface $form, ?string $groupId): void
    {
        /** @var RecipeCreateFormDataValidation */
        $formData = $form->getData();

        try {
            /** @var User */
            $userSession = $this->security->getUser();

            $recipeId = $this->recipeRepository->uuidCreate();
            $form->uploadFiles($request, $this->appConfigRecipeUploadedPath);
            $recipeEntity = $this->recipeCreateFormDataMapper->toEntity($formData, $userSession, $recipeId, $groupId);

            if (null !== $recipeEntity->getImage()) {
                $this->image->resizeToAFrame(
                    $this->appConfigRecipeUploadedPath.'/'.$recipeEntity->getImage(),
                    $this->appConfigImageSaveSizeWidth,
                    $this->appConfigImageSaveSizeHeight
                );
            }

            $this->recipeRepository->save($recipeEntity);
        } catch (\Throwable $th) {
            throw RecipeCreateException::fromMessage($th->getMessage())->log();
        }
    }
}
