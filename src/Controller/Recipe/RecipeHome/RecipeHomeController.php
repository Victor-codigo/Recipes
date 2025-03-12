<?php

declare(strict_types=1);

namespace App\Controller\Recipe\RecipeHome;

use App\Controller\Exception\UserSessionNotFoundException;
use App\Controller\Recipe\RecipeCreate\RecipeCreateController;
use App\Controller\Recipe\RecipeModify\RecipeModifyController;
use App\Entity\Recipe;
use App\Entity\User;
use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormType;
use App\Form\Recipe\RecipeModify\RECIPE_MODIFY_FORM_FIELDS;
use App\Form\Recipe\RecipeModify\RecipeModifyFormType;
use App\Repository\Exception\DBNotFoundException;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Templates\Components\Recipe\RecipeHome\Home\RecipeHomeSectionComponentDto;
use App\Templates\Components\Recipe\RecipeHome\RecipeHomeComponentBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use VictorCodigo\SymfonyFormExtended\Factory\FormFactoryExtended;
use VictorCodigo\SymfonyFormExtended\Form\FormExtendedInterface;

#[Route(
    name: 'recipe_home',
    path: '/{_locale}/recipe/page-{page}-{pageItems}',
    methods: ['GET'],
    requirements: [
        '_locale' => 'en|es',
        'page' => '\d+',
        'pageItems' => '\d+',
    ]
)]
class RecipeHomeController extends AbstractController
{
    /**
     * @param FormFactoryExtended<RecipeCreateFormType> $formFactory
     */
    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
        private RecipeRepository $recipeRepository,
        private RouterInterface $router,
        private FormFactoryExtended $formFactory,
        private readonly int $appConfigPaginationPageMaxItems,
        private readonly string $appConfigRecipeImageNotImagePublicPath,
        private readonly string $appConfigRecipePublicUploadedPath,
    ) {
    }

    /**
     * @throws DBNotFoundException
     * @throws UserSessionNotFoundException
     */
    public function __invoke(int $page, int $pageItems): Response
    {
        $userSession = $this->getUserSession();
        $recipes = $this->getRecipesFromDb($userSession->getId(), $page, $pageItems);
        $recipesUsers = $this->getRecipesUsersFromDb($recipes);
        /** @var FormExtendedInterface<RecipeCreateFormType> */
        $recipesCreateForm = $this->formFactory->createNamedExtended(RECIPE_CREATE_FORM_FIELDS::FORM_NAME->value, RecipeCreateFormType::class);
        $recipesModifyForm = $this->formFactory->createNamedExtended(RECIPE_MODIFY_FORM_FIELDS::FORM_NAME->value, RecipeModifyFormType::class);

        return $this->createView($recipesCreateForm, $recipesModifyForm, $recipes, $recipesUsers);
    }

    /**
     * @throws UserSessionNotFoundException
     */
    private function getUserSession(): User
    {
        /** @var (User&UserInterface)|null $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw UserSessionNotFoundException::fromMessage('User not logged in');
        }

        return $user;
    }

    /**
     * @return Collection<array-key, Recipe>
     *
     * @throws DBNotFoundException
     */
    private function getRecipesFromDb(string $userId, int $page, int $pageItems): Collection
    {
        try {
            $recipesPaginator = $this->recipeRepository->findRecipesByUserIdOrFail($userId, null, $page, $pageItems);

            return new ArrayCollection(\iterator_to_array($recipesPaginator));
        } catch (\Throwable $th) {
            return new ArrayCollection([]);
        }
    }

    /**
     * @param Collection<int, Recipe> $recipes
     *
     * @return Collection<int, User>
     */
    private function getRecipesUsersFromDb(Collection $recipes): Collection
    {
        try {
            $recipesUsersId = $recipes->map(fn (Recipe $recipe): string => $recipe->getUserId());
            $users = $this->userRepository->findUsersByIdOrFail($recipesUsersId, 1, $this->appConfigPaginationPageMaxItems);

            return new ArrayCollection(iterator_to_array($users->getIterator()));
        } catch (\Throwable $th) {
            return new ArrayCollection([]);
        }
    }

    /**
     * @param FormExtendedInterface<RecipeCreateFormType> $recipeCreateForm
     * @param Collection<int, Recipe>                     $recipes
     * @param Collection<int, User>                       $recipesUsers
     * @param Collection<int, string>                     $messagesOk
     * @param Collection<int, string>                     $messagesError
     */
    private function createRecipeHomeSectionComponentDto(FormExtendedInterface $recipeCreateForm, FormExtendedInterface $recipeModifyForm, Collection $recipes, Collection $recipesUsers, Collection $messagesOk, Collection $messagesError): RecipeHomeSectionComponentDto
    {
        /** @var RecipeCreateFormType */
        $recipeCreateFormType = $recipeCreateForm->getConfig()->getType()->getInnerType();
        /** @var RecipeModifyFormType */
        $recipeModifyFormType = $recipeModifyForm->getConfig()->getType()->getInnerType();
        $validForm = !$messagesOk->isEmpty() || !$messagesError->isEmpty();

        return new RecipeHomeComponentBuilder($this->appConfigRecipeImageNotImagePublicPath, $this->appConfigRecipePublicUploadedPath)
            ->title('Page title', 'title path')
            ->validation($validForm)
            ->errors($messagesOk->toArray(), $messagesError->toArray())
            ->listItems($recipes, $recipesUsers)
            ->pagination(1, 20, 1)
            ->searchBar(
                'group id',
                'search value',
                'name filter',
                'csrf token',
                'search autocomplete url',
                'search form action url'
            )
            ->recipeCreateFormModal($recipeCreateFormType->getCsrfToken(), $this->router->generate('recipe_create'))
            ->recipeModifyFormModal($recipeModifyFormType->getCsrfToken(), $this->router->generate('recipe_modify'))
            ->recipeRemoveFormModal('', '')
            ->recipeRemoveMultiFormModal('', '')
            ->build();
    }

    /**
     * @param FormExtendedInterface<RecipeCreateFormType> $recipesCreateForm
     * @param Collection<int, Recipe>                     $recipes
     * @param Collection<int, User>                       $recipesUsers
     */
    private function createView(FormExtendedInterface $recipesCreateForm, FormExtendedInterface $recipeModifyForm, Collection $recipes, Collection $recipesUsers): Response
    {
        $messagesOk = new ArrayCollection([
            ...$recipesCreateForm->getFlashMessages(RecipeCreateController::FORM_FLASH_BAG_MESSAGES_SUCCESS),
            ...$recipesCreateForm->getFlashMessages(RecipeModifyController::FORM_FLASH_BAG_MESSAGES_SUCCESS),
        ]);

        $messagesError = new ArrayCollection([
            ...$recipesCreateForm->getFlashMessages(RecipeCreateController::FORM_FLASH_BAG_MESSAGES_ERROR),
            ...$recipesCreateForm->getFlashMessages(RecipeModifyController::FORM_FLASH_BAG_MESSAGES_ERROR),
        ]);

        $recipeHomeSectionComponentDto = $this->createRecipeHomeSectionComponentDto(
            $recipesCreateForm,
            $recipeModifyForm,
            $recipes,
            $recipesUsers,
            $messagesOk,
            $messagesError,
        );

        return $this->render('Recipe/Home/index.html.twig', [
            'recipeHomeSectionComponentDto' => $recipeHomeSectionComponentDto,
        ]);
    }
}
