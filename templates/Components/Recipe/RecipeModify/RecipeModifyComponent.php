<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeModify;

use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use App\Templates\Components\DropZone\DropZoneComponent;
use App\Templates\Components\DropZone\DropZoneComponentDto;
use App\Templates\Components\ImageAvatar\ImageAvatarComponentDto;
use App\Templates\Components\Recipe\RecipeItemAdd\RecipeItemAddComponentDto;
use App\Templates\Components\Recipe\RecipeItemAdd\TYPE_INPUT;
use App\Templates\Components\Title\TITLE_TYPE;
use App\Templates\Components\Title\TitleComponentDto;
use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'RecipeModifyComponent',
    template: 'Components/Recipe/RecipeModify/RecipeModifyComponent.html.twig'
)]
class RecipeModifyComponent extends TwigComponent
{
    private const string INGREDIENTS_SELECTOR = 'data-js-ingredients';
    private const string STEPS_SELECTOR = 'data-js-steps';

    public RecipeModifyComponentDto&TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;
    public readonly DropZoneComponentDto $imageDto;
    public readonly ImageAvatarComponentDto $imageRecipeAvatarDto;
    public readonly RecipeItemAddComponentDto $ingredientsItemAddDto;
    public readonly RecipeItemAddComponentDto $stepsItemAddDto;

    public function __construct(
        TranslatorInterface $translator,
        private readonly string $appConfigRecipeImageNotImagePublicPath,
    ) {
        parent::__construct($translator);
    }

    public static function getComponentName(): string
    {
        return 'RecipeModifyComponent';
    }

    public function mount(RecipeModifyComponentDto $data): void
    {
        $this->data = $data;

        $this->titleDto = $this->createTitleComponentDto();
        $this->imageDto = $this->createImageDropZone();
        $this->ingredientsItemAddDto = $this->createIngredientsItemAddDto();
        $this->stepsItemAddDto = $this->createStepsItemAddDto();
        $this->imageRecipeAvatarDto = $this->createRecipeImageAvatar();
    }

    private function createTitleComponentDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->translate('form.title'), TITLE_TYPE::POP_UP, null);
    }

    private function createImageDropZone(): DropZoneComponentDto
    {
        return new DropZoneComponentDto(
            DropZoneComponent::getComponentName(),
            $this->translate('field.image.label'),
            RECIPE_CREATE_FORM_FIELDS::getNameWithForm(RECIPE_CREATE_FORM_FIELDS::IMAGE),
            $this->translate('field.image.placeholder')
        );
    }

    private function createIngredientsItemAddDto(): RecipeItemAddComponentDto
    {
        return new RecipeItemAddComponentDto()
            ->component(
                self::INGREDIENTS_SELECTOR,
                $this->translate('field.ingredients.label'),
                $this->translate('field.ingredients.msg.error.not_blank')
            )
            ->items(
                TYPE_INPUT::INPUT,
                RECIPE_CREATE_FORM_FIELDS::getNameWithForm(RECIPE_CREATE_FORM_FIELDS::INGREDIENTS, true),
                $this->translate('field.ingredients.item.label'),
                $this->translate('field.ingredients.item.placeholder'),
                $this->translate('field.ingredients.item.msg.error')
            )
            ->buttonAdd(
                $this->translate('field.ingredients.button.add.label'),
                $this->translate('field.ingredients.button.remove.title')
            );
    }

    private function createStepsItemAddDto(): RecipeItemAddComponentDto
    {
        return new RecipeItemAddComponentDto()
            ->component(
                self::STEPS_SELECTOR,
                $this->translate('field.steps.label'),
                $this->translate('field.steps.msg.error.not_blank')
            )
            ->items(
                TYPE_INPUT::TEXTAREA,
                RECIPE_CREATE_FORM_FIELDS::getNameWithForm(RECIPE_CREATE_FORM_FIELDS::STEPS, true),
                $this->translate('field.steps.item.label'),
                $this->translate('field.steps.item.placeholder'),
                $this->translate('field.steps.item.msg.error')
            )
            ->buttonAdd(
                $this->translate('field.steps.button.add.label'),
                $this->translate('field.steps.button.remove.title')
            );
    }

    private function createRecipeImageAvatar(): ImageAvatarComponentDto
    {
        return new ImageAvatarComponentDto(
            '',
            $this->appConfigRecipeImageNotImagePublicPath,
            $this->translate('field.image.alt')
        );
    }
}
