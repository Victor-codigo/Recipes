<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeHome;

use App\Common\Config;
use App\Common\DtoBuilder\DtoBuilder;
use App\Common\DtoBuilder\DtoBuilderInterface;
use App\Entity\Recipe;
use App\Entity\User;
use App\Form\RECIPE_REMOVE_MULTI_FORM_FIELDS;
use App\Templates\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Templates\Components\HomeSection\Home\RemoveMultiFormDto;
use App\Templates\Components\HomeSection\SearchBar\SECTION_FILTERS;
use App\Templates\Components\HomeSection\SearchBar\SearchBarComponentDto;
use App\Templates\Components\Modal\ModalComponentDto;
use App\Templates\Components\Recipe\RecipeCreate\RecipeCreateComponent;
use App\Templates\Components\Recipe\RecipeCreate\RecipeCreateComponentDto;
use App\Templates\Components\Recipe\RecipeHome\Home\RecipeHomeSectionComponentDto;
use App\Templates\Components\Recipe\RecipeHome\ListItem\RecipeListItemComponent;
use App\Templates\Components\Recipe\RecipeHome\ListItem\RecipeListItemComponentDto;
use Doctrine\Common\Collections\Collection;

class RecipeHomeComponentBuilder implements DtoBuilderInterface
{
    private const RECIPE_CREATE_MODAL_ID = 'recipe_create_modal';
    private const RECIPE_REMOVE_MULTI_MODAL_ID = 'recipe_remove_multi_modal';
    public const RECIPE_DELETE_MODAL_ID = 'recipe_delete_modal';
    public const RECIPE_MODIFY_MODAL_ID = 'recipe_modify_modal';
    public const RECIPE_INFO_MODAL_ID = 'recipe_info_modal';

    private const RECIPE_HOME_COMPONENT_NAME = 'RecipeHomeComponent';
    private const RECIPE_HOME_LIST_COMPONENT_NAME = 'RecipeHomeListComponent';
    private const RECIPE_HOME_LIST_ITEM_COMPONENT_NAME = 'RecipeHomeListItemComponent';

    private readonly DtoBuilder $builder;
    private readonly HomeSectionComponentDto $homeSectionComponentDto;
    private readonly ModalComponentDto $recipeInfoModalDto;

    private readonly Collection $recipes;
    private readonly Collection $recipesUsers;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'recipeCreateModal',
            'recipeModifyFormModal',
            'recipeRemoveMultiModal',
            'recipeRemoveFormModal',
            'errors',
            'pagination',
            'listItems',
            'validation',
            'searchBar',
        ]);

        $this->homeSectionComponentDto = new HomeSectionComponentDto();
    }

    public function title(?string $title, ?string $titlePath): self
    {
        $this->builder->setMethodStatus('title', true);

        $this->homeSectionComponentDto->title($title, $titlePath);

        return $this;
    }

    public function recipeCreateFormModal(string $recipeCreateFormCsrfToken, string $recipeCreateFormActionUrl): self
    {
        $this->builder->setMethodStatus('recipeCreateModal', true);

        $this->homeSectionComponentDto->createFormModal(
            $this->createRecipeCreateComponentDto($recipeCreateFormCsrfToken, $recipeCreateFormActionUrl)
        );

        return $this;
    }

    public function recipeModifyFormModal(string $recipeModifyFormCsrfToken, string $recipeModifyFormActionUrl): self
    {
        $this->builder->setMethodStatus('recipeModifyFormModal', true);

        $this->homeSectionComponentDto->modifyFormModal(
            $this->createRecipeModifyModalDto($recipeModifyFormCsrfToken, $recipeModifyFormActionUrl)
        );

        return $this;
    }

    public function recipeRemoveMultiFormModal(string $recipeRemoveMultiFormCsrfToken, string $recipeRemoveMultiFormActionUrl): self
    {
        $this->builder->setMethodStatus('recipeRemoveMultiModal', true);

        $this->homeSectionComponentDto->removeMultiFormModal(
            $this->createRecipeRemoveMultiComponentDto($recipeRemoveMultiFormCsrfToken, $recipeRemoveMultiFormActionUrl)
        );

        return $this;
    }

    public function recipeRemoveFormModal(string $recipeRemoveFormCsrfToken, string $recipeRemoveFormActionUrl): self
    {
        $this->builder->setMethodStatus('recipeRemoveFormModal', true);

        $this->homeSectionComponentDto->removeFormModal(
            $this->createRecipeRemoveModalDto($recipeRemoveFormCsrfToken, $recipeRemoveFormActionUrl)
        );

        return $this;
    }

    public function errors(array $recipeSectionValidationOk, array $recipeValidationErrorsMessage): self
    {
        $this->builder->setMethodStatus('errors', true);

        $this->homeSectionComponentDto->errors($recipeSectionValidationOk, $recipeValidationErrorsMessage);

        return $this;
    }

    public function pagination(int $page, int $pageItems, int $pagesTotal): self
    {
        $this->builder->setMethodStatus('pagination', true);

        $this->homeSectionComponentDto->pagination($page, $pageItems, $pagesTotal);

        return $this;
    }

    public function listItems(Collection $listRecipesData, Collection $recipesUsers): self
    {
        $this->builder->setMethodStatus('listItems', true);

        $this->recipes = $listRecipesData;
        $this->recipesUsers = $recipesUsers;

        return $this;
    }

    public function validation(bool $validForm): self
    {
        $this->builder->setMethodStatus('validation', true);

        $this->homeSectionComponentDto->validation(
            $validForm,
        );

        return $this;
    }

    public function searchBar(
        string $groupId,
        ?string $searchValue,
        ?string $nameFilterValue,
        string $searchBarCsrfToken,
        string $searchAutoCompleteUrl,
        string $searchBarFormActionUrl,
    ): self {
        $this->builder->setMethodStatus('searchBar', true);

        $this->homeSectionComponentDto->searchBar(new SearchBarComponentDto(
            $groupId,
            $searchValue,
            [SECTION_FILTERS::RECIPE],
            null,
            $nameFilterValue,
            $searchBarCsrfToken,
            $searchBarFormActionUrl,
            $searchAutoCompleteUrl,
        ));

        return $this;
    }

    public function build(): RecipeHomeSectionComponentDto
    {
        $this->builder->validate();

        $this->homeSectionComponentDto->translationDomainNames(
            self::RECIPE_HOME_COMPONENT_NAME,
            self::RECIPE_HOME_LIST_COMPONENT_NAME,
            self::RECIPE_HOME_LIST_ITEM_COMPONENT_NAME
        );
        $this->homeSectionComponentDto->createRemoveMultiForm(
            $this->createRemoveMultiFormDto()
        );
        $this->homeSectionComponentDto->listItems(
            RecipeListItemComponent::getComponentName(),
            $this->createRecipeListItemComponentDto($this->recipes, $this->recipesUsers)->toArray(),
            Config::RECIPE_IMAGE_NO_IMAGE_PUBLIC_PATH
        );
        $this->homeSectionComponentDto->display(
            true,
            false
        );
        $this->recipeInfoModalDto = $this->createRecipeInfoModalDto();

        return $this->createRecipeHomeSectionComponentDto($this->recipeInfoModalDto);
    }

    private function createRecipeCreateComponentDto(string $recipeCreateFormCsrfToken, string $recipeCreateFormActionUrl): ModalComponentDto
    {
        $homeSectionCreateComponentDto = new RecipeCreateComponentDto()
            ->validation(false, [])
            ->form(
                $recipeCreateFormCsrfToken,
                mb_strtolower($recipeCreateFormActionUrl)
            )
            ->formFields(
                '',
                null,
                [],
                [],
                null,
                null,
                null,
                false
            );

        return new ModalComponentDto(
            self::RECIPE_CREATE_MODAL_ID,
            '',
            false,
            RecipeCreateComponent::getComponentName(),
            $homeSectionCreateComponentDto,
            []
        );
    }

    private function createRecipeRemoveMultiComponentDto(string $recipeRemoveMultiFormCsrfToken, string $recipeRemoveFormActionUrl): ModalComponentDto
    {
        // $homeSectionRemoveMultiComponentDto = new RecipeRemoveComponentDto(
        //     RecipeRemoveComponent::getComponentName(),
        //     [],
        //     $recipeRemoveMultiFormCsrfToken,
        //     mb_strtolower($recipeRemoveFormActionUrl),
        //     true,
        // );

        // return new ModalComponentDto(
        //     self::RECIPE_REMOVE_MULTI_MODAL_ID,
        //     '',
        //     false,
        //     RecipeRemoveComponent::getComponentName(),
        //     $homeSectionRemoveMultiComponentDto,
        //     []
        // );
        return $this->createFakeModalComponentDto();
    }

    private function createRecipeRemoveModalDto(string $recipeRemoveFormCsrfToken, string $recipeRemoveFormActionUrl): ModalComponentDto
    {
        // $homeModalDelete = new RecipeRemoveComponentDto(
        //     RecipeRemoveComponent::getComponentName(),
        //     [],
        //     $recipeRemoveFormCsrfToken,
        //     mb_strtolower($recipeRemoveFormActionUrl),
        //     false
        // );

        // return new ModalComponentDto(
        //     self::RECIPE_DELETE_MODAL_ID,
        //     '',
        //     false,
        //     RecipeRemoveComponent::getComponentName(),
        //     $homeModalDelete,
        //     []
        // );

        return $this->createFakeModalComponentDto();
    }

    private function createRecipeModifyModalDto(string $recipeModifyFormCsrfToken, string $recipeModifyFormActionUrlPlaceholder): ModalComponentDto
    {
        // $homeModalModify = new RecipeModifyComponentDto(
        //     [],
        //     '{name_placeholder}',
        //     '{address_placeholder}',
        //     '{description_placeholder}',
        //     '{image_placeholder}',
        //     Config::RECIPE_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
        //     $recipeModifyFormCsrfToken,
        //     false,
        //     mb_strtolower($recipeModifyFormActionUrlPlaceholder)
        // );

        // return new ModalComponentDto(
        //     self::RECIPE_MODIFY_MODAL_ID,
        //     '',
        //     false,
        //     RecipeModifyComponent::getComponentName(),
        //     $homeModalModify,
        //     []
        // );

        return $this->createFakeModalComponentDto();
    }

    private function createRemoveMultiFormDto(): RemoveMultiFormDto
    {
        return new RemoveMultiFormDto(
            RECIPE_REMOVE_MULTI_FORM_FIELDS::FORM,
            sprintf('%s[%s]', RECIPE_REMOVE_MULTI_FORM_FIELDS::FORM, RECIPE_REMOVE_MULTI_FORM_FIELDS::TOKEN),
            sprintf('%s[%s]', RECIPE_REMOVE_MULTI_FORM_FIELDS::FORM, RECIPE_REMOVE_MULTI_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s][]', RECIPE_REMOVE_MULTI_FORM_FIELDS::FORM, RECIPE_REMOVE_MULTI_FORM_FIELDS::RECIPES_ID),
            self::RECIPE_REMOVE_MULTI_MODAL_ID
        );
    }

    private function createRecipeInfoModalDto(): ModalComponentDto
    {
        // $recipeInfoComponentDto = new RecipeInfoComponentDto(
        //     RecipeInfoComponent::getComponentName()
        // );

        // return new ModalComponentDto(
        //     self::RECIPE_INFO_MODAL_ID,
        //     '',
        //     false,
        //     RecipeInfoComponent::getComponentName(),
        //     $recipeInfoComponentDto,
        //     []
        // );

        return $this->createFakeModalComponentDto();
    }

    private function createFakeModalComponentDto(): ModalComponentDto
    {
        return new ModalComponentDto(
            '',
            '',
            false,
            '',
            '',
            []
        );
    }

    /**
     * @param Collection<Recipe> $recipes
     * @param Collection<User>   $users
     *
     * @return Collection<Recipe>
     *
     * @throws \LogicException
     */
    private function createRecipeListItemComponentDto(Collection $recipes, Collection $users): Collection
    {
        return $recipes->map(static function (Recipe $recipeEntity) use ($users): RecipeListItemComponentDto {
            /** @var User|null $recipeUser */
            $recipeUser = $users->findFirst(
                fn (int $index, User $user): bool => $user->getId() === $recipeEntity->getUserId()
            );

            if (null === $recipeUser) {
                throw new \LogicException('Recipe must have a user owner');
            }

            return new RecipeListItemComponentDto(
                RecipeListItemComponent::getComponentName(),
                $recipeEntity->getId(),
                $recipeUser->getName(),
                $recipeEntity->getName(),
                $recipeEntity->getCategory(),
                $recipeEntity->getImage(),
                $recipeEntity->getRating(),
                $recipeEntity->toJson(),
                self::RECIPE_MODIFY_MODAL_ID,
                self::RECIPE_DELETE_MODAL_ID,
                self::RECIPE_INFO_MODAL_ID,
                RecipeListItemComponent::getComponentName()
            );
        });
    }

    private function createRecipeHomeSectionComponentDto(ModalComponentDto $recipeInfoModalDto): RecipeHomeSectionComponentDto
    {
        return (new RecipeHomeSectionComponentDto())
            ->homeSection($this->homeSectionComponentDto)
            ->recipeInfoModal($recipeInfoModalDto)
            ->build();
    }
}
