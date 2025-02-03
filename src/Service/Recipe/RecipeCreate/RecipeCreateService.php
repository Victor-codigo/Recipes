<?php

declare(strict_types=1);

namespace App\Service\Recipe\RecipeCreate;

use App\Entity\User;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormDataMapper;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormDataValidation;
use App\Repository\RecipeRepository;
use App\Service\Exception\RecipeCreateException;
use Symfony\Bundle\SecurityBundle\Security;

class RecipeCreateService
{
    public function __construct(
        private RecipeRepository $recipeRepository,
        private Security $security,
        private RecipeCreateFormDataMapper $recipeCreateFormDataMapper,
    ) {
    }

    /**
     * @throws RecipeCreateException
     */
    public function __invoke(RecipeCreateFormDataValidation $formData, ?string $groupId): void
    {
        /** @var User|null */
        $userSession = $this->security->getUser();

        if (null === $userSession) {
            throw RecipeCreateException::fromMessage('User session not found');
        }

        $recipeId = $this->recipeRepository->uuidCreate();
        $recipeEntity = $this->recipeCreateFormDataMapper->toEntity($formData, $userSession, $recipeId, $groupId);

        try {
            $this->recipeRepository->save($recipeEntity);
        } catch (\Throwable $th) {
            throw RecipeCreateException::fromMessage($th->getMessage());
        }
    }
}
