<?php

declare(strict_types=1);

namespace App\Controller\Recipe\RecipeHome;

use App\Controller\Exception\UserSessionNotFoundException;
use App\Controller\Recipe\RecipeCreate\RecipeCreateController;
use App\Controller\Recipe\RecipeModify\RecipeModifyController;
use App\Controller\Recipe\RecipeRemove\RecipeRemoveController;
use App\Entity\Recipe;
use App\Entity\User;
use App\Form\HomeSection\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Form\HomeSection\SearchBar\SEARCH_TEXT_FILTER;
use App\Form\HomeSection\SearchBar\SearchFormDataValidation;
use App\Form\HomeSection\SearchBar\SearchFormType;
use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use App\Form\Recipe\RecipeCreate\RecipeCreateFormType;
use App\Form\Recipe\RecipeModify\RECIPE_MODIFY_FORM_FIELDS;
use App\Form\Recipe\RecipeModify\RecipeModifyFormType;
use App\Form\Recipe\RecipeRemove\RECIPE_REMOVE_FORM_FIELDS;
use App\Form\Recipe\RecipeRemove\RecipeRemoveFormType;
use App\Repository\Exception\DBNotFoundException;
use App\Repository\UserRepository;
use App\Service\Recipe\RecipeFind\RecipeFindService;
use App\Service\Recipe\RecipeFind\RecipeFindServiceDto;
use App\Templates\Components\Recipe\RecipeHome\Home\RecipeHomeSectionComponentDto;
use App\Templates\Components\Recipe\RecipeHome\RecipeHomeComponentBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use VictorCodigo\SymfonyFormExtended\Factory\FormFactoryExtended;
use VictorCodigo\SymfonyFormExtended\Form\FormExtendedInterface;

#[Route(
    name: 'recipe_home',
    path: '/{_locale}/recipe/page-{page}-{pageItems}',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
        'page' => '\d+',
        'pageItems' => '\d+',
    ]
)]
class RecipeHomeController extends AbstractController
{
    private const string RECIPE_SEARCH_FORM_FLASH_BAG_MESSAGES_SUCCESS = 'recipeSearchForm.success';
    private const string RECIPE_SEARCH_FORM_FLASH_BAG_MESSAGES_ERROR = 'recipeSearchForm.error';

    /**
     * @param FormFactoryExtended<RecipeCreateFormType> $formFactory
     */
    public function __construct(
        private Security $security,
        private RouterInterface $router,
        private FormFactoryExtended $formFactory,
        private RecipeFindService $recipeFindService,
        private UserRepository $userRepository,
        private readonly int $appConfigPaginationPageMaxItems,
        private readonly string $appConfigRecipeImageNotImagePublicPath,
        private readonly string $appConfigRecipePublicUploadedPath,
    ) {
    }

    /**
     * @throws DBNotFoundException
     * @throws UserSessionNotFoundException
     */
    public function __invoke(Request $request): Response
    {
        $page = $request->attributes->getInt('page', 1);
        $pageItems = $request->attributes->getInt('pageItems', $this->appConfigPaginationPageMaxItems) > $this->appConfigPaginationPageMaxItems
            ? $this->appConfigPaginationPageMaxItems
            : $request->attributes->getInt('pageItems', $this->appConfigPaginationPageMaxItems);

        $recipesCreateForm = $this->formFactory->createNamedExtended(RECIPE_CREATE_FORM_FIELDS::FORM_NAME->value, RecipeCreateFormType::class);
        $recipesModifyForm = $this->formFactory->createNamedExtended(RECIPE_MODIFY_FORM_FIELDS::FORM_NAME->value, RecipeModifyFormType::class);
        $recipesRemoveForm = $this->formFactory->createNamedExtended(RECIPE_REMOVE_FORM_FIELDS::FORM_NAME->value, RecipeRemoveFormType::class);
        $recipesSearchForm = $this->formFactory->createNamedExtended(SEARCHBAR_FORM_FIELDS::FORM_NAME->value, SearchFormType::class);

        $recipesSearchFormData = $this->manageRecipeSearchBar($recipesSearchForm, $request);
        $userSession = $this->getUserSession();
        $recipes = $this->recipeFindService->__invoke(new RecipeFindServiceDto(
            $userSession->getId(),
            null,
            null,
            $page,
            $pageItems,
            $recipesSearchFormData
        ));
        $recipesUsers = $this->getRecipesUsers($recipes);

        $errors = $recipesSearchForm->getErrors(true);

        return $this->createView($recipesCreateForm, $recipesModifyForm, $recipesRemoveForm, $recipesSearchForm, $recipes, $recipesUsers);
    }

    /**
     * @throws UserSessionNotFoundException
     */
    private function getUserSession(): User
    {
        /** @var (User&UserInterface)|null $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw UserSessionNotFoundException::fromMessage('User not logged in')->log();
        }

        return $user;
    }

    /**
     * @param Collection<int, Recipe> $recipes
     *
     * @return Collection<int, User>
     */
    private function getRecipesUsers(Collection $recipes): Collection
    {
        try {
            $recipesUsersId = $recipes
                ->map(fn (Recipe $recipe): string => $recipe->getUserId())
                ->reduce(function (Collection $usersAccumulated, string $userId): Collection {
                    $usersAccumulated->contains($userId) ?: $usersAccumulated->add($userId);

                    return $usersAccumulated;
                },
                    new ArrayCollection([])
                );
            $users = $this->userRepository->findUsersByIdOrFail($recipesUsersId, 1, $this->appConfigPaginationPageMaxItems);

            return new ArrayCollection(iterator_to_array($users->getIterator()));
        } catch (\Throwable $th) {
            return new ArrayCollection([]);
        }
    }

    public function manageRecipeSearchBar(FormExtendedInterface $recipeSearchForm, Request $request): ?SearchFormDataValidation
    {
        $recipeSearchForm->handleRequest($request);

        if ($recipeSearchForm->isSubmitted() && $recipeSearchForm->isValid()) {
            /** @var SearchFormDataValidation */
            $formData = $recipeSearchForm->getData();

            return $formData;
        }

        return null;
    }

    /**
     * @return array{
     *  field_filter: string,
     *  name_filter: string,
     *  search_value: string
     * }
     */
    private function parseRecipeSearchFormData(FormExtendedInterface $recipeSearchForm): array
    {
        /** @var SearchFormDataValidation|null */
        $recipeSearchFormData = $recipeSearchForm->getData();
        $recipeSearchFormDataReturn = [
            SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => '',
            SEARCHBAR_FORM_FIELDS::NAME_FILTER->value => '',
            SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => '',
        ];

        if (null !== $recipeSearchFormData) {
            $recipeSearchFormDataReturn = [
                SEARCHBAR_FORM_FIELDS::FIELD_FILTER->value => $recipeSearchFormData->field_filter->value,
                SEARCHBAR_FORM_FIELDS::NAME_FILTER->value => isset($recipeSearchFormData->name_filter) ? $recipeSearchFormData->name_filter->value : SEARCH_TEXT_FILTER::EQUALS->value,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE->value => $recipeSearchFormData->search_value,
            ];
        }

        return $recipeSearchFormDataReturn;
    }

    /**
     * @param FormExtendedInterface<RecipeCreateFormType> $recipeCreateForm
     * @param Collection<int, Recipe>                     $recipes
     * @param Collection<int, User>                       $recipesUsers
     * @param Collection<int, string>                     $messagesOk
     * @param Collection<int, string>                     $messagesError
     */
    private function createRecipeHomeSectionComponentDto(FormExtendedInterface $recipeCreateForm, FormExtendedInterface $recipeModifyForm, FormExtendedInterface $recipeRemoveForm, FormExtendedInterface $recipeSearchForm, Collection $recipes, Collection $recipesUsers, Collection $messagesOk, Collection $messagesError): RecipeHomeSectionComponentDto
    {
        /** @var RecipeCreateFormType */
        $recipeCreateFormType = $recipeCreateForm->getConfig()->getType()->getInnerType();
        /** @var RecipeModifyFormType */
        $recipeModifyFormType = $recipeModifyForm->getConfig()->getType()->getInnerType();
        /** @var RecipeRemoveFormType */
        $recipeRemoveFormType = $recipeRemoveForm->getConfig()->getType()->getInnerType();
        /** @var SearchFormType */
        $recipeSearchFormType = $recipeSearchForm->getConfig()->getType()->getInnerType();
        $recipeSearchFormData = $this->parseRecipeSearchFormData($recipeSearchForm);
        $validForm = !$messagesOk->isEmpty() || !$messagesError->isEmpty();

        return new RecipeHomeComponentBuilder($this->appConfigRecipeImageNotImagePublicPath, $this->appConfigRecipePublicUploadedPath)
            ->title('Page title', 'title path')
            ->validation($validForm)
            ->errors($messagesOk->toArray(), $messagesError->toArray())
            ->listItems($recipes, $recipesUsers)
            ->pagination(1, 20, 1)
            ->searchBar(
                '',
                $recipeSearchFormData['search_value'],
                $recipeSearchFormData['name_filter'],
                $recipeSearchFormData['field_filter'],
                $recipeSearchFormType->getCsrfToken(),
                '',
                $this->router->generate('recipe_home', [
                    'page' => 1,
                    'pageItems' => $this->appConfigPaginationPageMaxItems,
                ]),
            )
            ->recipeCreateFormModal($recipeCreateFormType->getCsrfToken(), $this->router->generate('recipe_create'))
            ->recipeModifyFormModal($recipeModifyFormType->getCsrfToken(), $this->router->generate('recipe_modify'))
            ->recipeRemoveFormModal($recipeRemoveFormType->getCsrfToken(), $this->router->generate('recipe_remove'))
            ->recipeRemoveMultiFormModal($recipeRemoveFormType->getCsrfToken(), $this->router->generate('recipe_remove'))
            ->build();
    }

    /**
     * @param FormExtendedInterface<RecipeCreateFormType> $recipesCreateForm
     * @param Collection<int, Recipe>                     $recipes
     * @param Collection<int, User>                       $recipesUsers
     */
    private function createView(FormExtendedInterface $recipesCreateForm, FormExtendedInterface $recipeModifyForm, FormExtendedInterface $recipesRemoveForm, FormExtendedInterface $recipeSearchForm, Collection $recipes, Collection $recipesUsers): Response
    {
        $messagesOk = new ArrayCollection([
            ...$recipesCreateForm->getFlashMessages(RecipeCreateController::FORM_FLASH_BAG_MESSAGES_SUCCESS),
            ...$recipesCreateForm->getFlashMessages(RecipeModifyController::FORM_FLASH_BAG_MESSAGES_SUCCESS),
            ...$recipesRemoveForm->getFlashMessages(RecipeRemoveController::FORM_FLASH_BAG_MESSAGES_SUCCESS),
            ...$recipeSearchForm->getFlashMessages(self::RECIPE_SEARCH_FORM_FLASH_BAG_MESSAGES_SUCCESS),
        ]);

        $messagesError = new ArrayCollection([
            ...$recipesCreateForm->getFlashMessages(RecipeCreateController::FORM_FLASH_BAG_MESSAGES_ERROR),
            ...$recipesCreateForm->getFlashMessages(RecipeModifyController::FORM_FLASH_BAG_MESSAGES_ERROR),
            ...$recipesRemoveForm->getFlashMessages(RecipeRemoveController::FORM_FLASH_BAG_MESSAGES_ERROR),
            ...$recipeSearchForm->getFlashMessages(self::RECIPE_SEARCH_FORM_FLASH_BAG_MESSAGES_ERROR),
        ]);

        $recipeHomeSectionComponentDto = $this->createRecipeHomeSectionComponentDto(
            $recipesCreateForm,
            $recipeModifyForm,
            $recipesRemoveForm,
            $recipeSearchForm,
            $recipes,
            $recipesUsers,
            $messagesOk,
            $messagesError,
        );

        $response = $this->render('Recipe/Home/index.html.twig', [
            'recipeHomeSectionComponentDto' => $recipeHomeSectionComponentDto,
        ]);

        if ($recipeSearchForm->isSubmitted() && $recipeSearchForm->isValid()) {
            $response->setStatusCode(Response::HTTP_SEE_OTHER);
        }

        return $response;
    }
}
