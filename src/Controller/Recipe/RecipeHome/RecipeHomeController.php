<?php

declare(strict_types=1);

namespace App\Controller\Recipe\RecipeHome;

use App\Common\Config;
use App\Controller\Exception\UserSessionNotFoundException;
use App\Controller\Recipe\RecipeCreate\RecipeCreateController;
use App\Entity\Recipe;
use App\Entity\User;
use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormType;
use App\Repository\Exception\DBNotFoundException;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Templates\Components\Recipe\RecipeHome\Home\RecipeHomeSectionComponentDto;
use App\Templates\Components\Recipe\RecipeHome\RecipeHomeComponentBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
        private RecipeRepository $recipeRepository,
        private RouterInterface $router,
        private FormFactoryInterface $formFactory,
    ) {
    }

    /**
     * @throws DBNotFoundException
     * @throws UserSessionNotFoundException
     */
    public function __invoke(int $page, int $pageItems, SessionInterface $session): Response
    {
        $userSession = $this->getUserSession();
        $recipes = $this->getRecipesFromDb($userSession->getId(), $page, $pageItems);
        $recipesUsers = $this->getRecipesUsersFromDb($recipes);
        /** @var FormInterface<RecipeCreateFormType> */
        $recipesCreateForm = $this->formFactory->createNamed(RECIPE_CREATE_FORM_FIELDS::FORM_NAME->value, RecipeCreateFormType::class);

        if (!$session instanceof Session) {
            throw UserSessionNotFoundException::fromMessage('User session not found');
        }

        return $this->createView($recipesCreateForm, $recipes, $recipesUsers, $session->getFlashBag());
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
            $recipesPaginator = $this->recipeRepository->getRecipesByUserIdOrFail($userId, null, $page, $pageItems);

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
            $users = $this->userRepository->findUsersByIdOrFail($recipesUsersId, 1, Config::PAGINATION_PAGE_MAX_ITEMS);

            return new ArrayCollection(iterator_to_array($users->getIterator()));
        } catch (\Throwable $th) {
            return new ArrayCollection([]);
        }
    }

    /**
     * @param FormInterface<RecipeCreateFormType> $form
     * @param Collection<int, Recipe>             $recipes
     * @param Collection<int, User>               $recipesUsers
     * @param string[]                            $messagesOk
     * @param string[]                            $messagesError
     */
    private function createRecipeHomeSectionComponentDto(FormInterface $form, Collection $recipes, Collection $recipesUsers, array $messagesOk, array $messagesError): RecipeHomeSectionComponentDto
    {
        /** @var RecipeCreateFormType */
        $formType = $form->getConfig()->getType()->getInnerType();
        $validForm = !empty($messagesOk) || !empty($messagesError);

        return new RecipeHomeComponentBuilder()
            ->title('Page title', 'title path')
            ->validation($validForm)
            ->errors($messagesOk, $messagesError)
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
            ->recipeCreateFormModal($formType->getCsrfToken(), $this->router->generate('recipe_create'))
            ->recipeModifyFormModal('', '')
            ->recipeRemoveFormModal('', '')
            ->recipeRemoveMultiFormModal('', '')
            ->build();
    }

    /**
     * @param FormInterface<RecipeCreateFormType> $form
     * @param Collection<int, Recipe>             $recipes
     * @param Collection<int, User>               $recipesUsers
     */
    private function createView(FormInterface $form, Collection $recipes, Collection $recipesUsers, FlashBagInterface $flashBagMessages): Response
    {
        $recipeHomeSectionComponentDto = $this->createRecipeHomeSectionComponentDto(
            $form,
            $recipes,
            $recipesUsers,
            // @phpstan-ignore argument.type
            $flashBagMessages->get(RecipeCreateController::FORM_FLASH_BAG_MESSAGES_SUCCESS),
            // @phpstan-ignore argument.type
            $flashBagMessages->get(RecipeCreateController::FORM_FLASH_BAG_MESSAGES_ERROR),
        );

        return $this->render('Recipe/Home/index.html.twig', [
            'recipeHomeSectionComponentDto' => $recipeHomeSectionComponentDto,
        ]);
    }
}
