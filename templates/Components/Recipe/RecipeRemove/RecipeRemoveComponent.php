<?php

declare(strict_types=1);

namespace App\Templates\Components\Recipe\RecipeRemove;

use App\Form\Recipe\RecipeRemoveMulti\RECIPE_REMOVE_MULTI_FORM_FIELDS;
use App\Form\Recipe\RecipeRemove\RECIPE_REMOVE_FORM_FIELDS;
use App\Templates\Components\HomeSection\ItemRemove\ItemRemoveComponent;
use App\Templates\Components\HomeSection\ItemRemove\ItemRemoveComponentDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'RecipeRemoveComponent',
    template: 'Components/HomeSection/ItemRemove/ItemRemoveComponent.html.twig'
)]
class RecipeRemoveComponent extends ItemRemoveComponent
{
    public static function getComponentName(): string
    {
        return 'RecipeRemoveComponent';
    }

    public function mount(ItemRemoveComponentDto $data): void
    {
        $this->data = $data;

        [$formName, $submitFieldName, $itemsIdFieldName, $tokenCsrfFieldName, $messageAdvice, $itemCloseButtonLabel,$itemRemoveButton] = $this->data->removeMulti
            ? $this->createRemoveMultiData()
            : $this->createRemoveData();

        $this->initialize(
            self::getComponentName(),
            $this->translate('form.title'),
            $formName,
            $submitFieldName,
            $itemsIdFieldName,
            $tokenCsrfFieldName,
            $messageAdvice,
            $itemCloseButtonLabel,
            $itemRemoveButton
        );
        $this->titleDto = $this->createTitleDto();
    }

    private function createRemoveMultiData(): array
    {
        return [
            RECIPE_REMOVE_MULTI_FORM_FIELDS::FORM_NAME->value,
            RECIPE_REMOVE_MULTI_FORM_FIELDS::getNameWithForm(RECIPE_REMOVE_MULTI_FORM_FIELDS::SUBMIT),
            RECIPE_REMOVE_MULTI_FORM_FIELDS::getNameWithForm(RECIPE_REMOVE_MULTI_FORM_FIELDS::RECIPES_ID, true),
            RECIPE_REMOVE_MULTI_FORM_FIELDS::getNameWithForm(RECIPE_REMOVE_MULTI_FORM_FIELDS::CSRF_TOKEN),
            $this->translate('form.message_advice.text_multi'),
            $this->translate('form.buttons.close.label'),
            $this->translate('form.buttons.remove.label'),
        ];
    }

    private function createRemoveData(): array
    {
        return [
            RECIPE_REMOVE_FORM_FIELDS::FORM_NAME->value,
            RECIPE_REMOVE_FORM_FIELDS::getNameWithForm(RECIPE_REMOVE_FORM_FIELDS::SUBMIT),
            RECIPE_REMOVE_FORM_FIELDS::getNameWithForm(RECIPE_REMOVE_FORM_FIELDS::RECIPES_ID, true),
            RECIPE_REMOVE_FORM_FIELDS::getNameWithForm(RECIPE_REMOVE_FORM_FIELDS::CSRF_TOKEN),
            $this->translate('form.message_advice.text'),
            $this->translate('form.buttons.close.label'),
            $this->translate('form.buttons.remove.label'),
        ];
    }
}
