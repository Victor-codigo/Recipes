<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeCreate;

use App\Form\Recipe\RecipeCreate\RECIPE_CREATE_FORM_FIELDS;
use App\Templates\Components\AlertValidation\AlertValidationComponentDto;
use App\Templates\Components\DropZone\DropZoneComponent;
use App\Templates\Components\DropZone\DropZoneComponentDto;
use App\Templates\Components\Recipe\RecipeItemAdd\RecipeItemAddComponentDto;
use App\Templates\Components\Recipe\RecipeItemAdd\TYPE_INPUT;
use App\Templates\Components\Title\TITLE_TYPE;
use App\Templates\Components\Title\TitleComponentDto;
use App\Templates\Components\TwigComponent;
use App\Templates\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'RecipeCreateComponent',
    template: 'Components/Recipe/RecipeCreate/RecipeCreateComponent.html.twig'
)]
class RecipeCreateComponent extends TwigComponent
{
    private const string INGREDIENTS_SELECTOR = 'data-js-ingredients';
    private const string STEPS_SELECTOR = 'data-js-steps';

    public RecipeCreateComponentDto&TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;
    public readonly DropZoneComponentDto $imageDto;
    public readonly ?AlertValidationComponentDto $alertValidationDto;
    public readonly RecipeItemAddComponentDto $ingredientsItemAddDto;
    public readonly RecipeItemAddComponentDto $stepsItemAddDto;

    public static function getComponentName(): string
    {
        return 'RecipeCreateComponent';
    }

    public function mount(RecipeCreateComponentDto $data): void
    {
        $this->data = $data;

        $this->titleDto = $this->createTitleComponentDto();
        $this->imageDto = $this->createImageDropZone();
        $this->alertValidationDto = $this->createAlertValidationComponentDto();
        $this->ingredientsItemAddDto = $this->createIngredientsItemAddDto();
        $this->stepsItemAddDto = $this->createStepsItemAddDto();
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
            RECIPE_CREATE_FORM_FIELDS::IMAGE->value,
            $this->translate('field.image.placeholder')
        );
    }

    private function createIngredientsItemAddDto(): RecipeItemAddComponentDto
    {
        return new RecipeItemAddComponentDto()
            ->component(
                self::INGREDIENTS_SELECTOR,
                $this->translate('field.ingredients.label'),
                $this->translate('field.ingredients.msg.error')
            )
            ->items(
                TYPE_INPUT::INPUT,
                RECIPE_CREATE_FORM_FIELDS::INGREDIENTS->value,
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
                $this->translate('field.steps.msg.error')
            )
            ->items(
                TYPE_INPUT::TEXTAREA,
                RECIPE_CREATE_FORM_FIELDS::STEPS->value,
                $this->translate('field.steps.item.label'),
                $this->translate('field.steps.item.placeholder'),
                $this->translate('field.steps.item.msg.error')
            )
            ->buttonAdd(
                $this->translate('field.steps.button.add.label'),
                $this->translate('field.steps.button.remove.title')
            );
    }

    /**
     * @return string[]
     */
    public function loadErrorsTranslation(array $errors): array
    {
        $errorsLang = [];
        foreach ($errors as $field => $error) {
            $errorsLang[] = match ($field) {
                // RECIPE_CREATE_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
                // RECIPE_CREATE_FORM_ERRORS::ADDRESS->value => $this->translate('validation.error.address'),
                // RECIPE_CREATE_FORM_ERRORS::RECIPE_NAME_REPEATED->value => $this->translate('validation.error.recipe_name_repeated'),
                // RECIPE_CREATE_FORM_ERRORS::IMAGE->value => $this->translate('validation.error.image'),
                // RECIPE_CREATE_FORM_ERRORS::DESCRIPTION->value,
                // RECIPE_CREATE_FORM_ERRORS::GROUP_ERROR->value,
                // RECIPE_CREATE_FORM_ERRORS::GROUP_ID->value => $this->translate('validation.error.internal_server'),
                default => $this->translate('validation.error.internal_server'),
            };
        }

        return $errorsLang;
    }

    public function loadValidationOkTranslation(): string
    {
        return $this->translate('form.validation.ok');
    }

    private function createAlertValidationComponentDto(): ?AlertValidationComponentDto
    {
        $errorsLang = $this->loadErrorsTranslation($this->data->errors);

        if (empty($errorsLang)) {
            return null;
        }

        return new AlertValidationComponentDto(
            array_unique([$this->loadValidationOkTranslation()]),
            array_unique($errorsLang)
        );
    }
}
