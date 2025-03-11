<?php

declare(strict_types=1);

namespace App\Service\Recipe\RecipeCreate;

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
        private readonly string $appConfigUserRecipesUploadedPath,
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
            $form->uploadFiles($request, $this->appConfigUserRecipesUploadedPath);
            $recipeEntity = $this->recipeCreateFormDataMapper->toEntity($formData, $userSession, $recipeId, $groupId);
            $this->recipeRepository->save($recipeEntity);
        } catch (\Throwable $th) {
            throw RecipeCreateException::fromMessage($th->getMessage());
        }
    }
}
