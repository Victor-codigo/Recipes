<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeModify;

enum RECIPE_MODIFY_FORM_FIELDS: string
{
    case FORM_NAME = 'recipe_modify_form';
    case ID = 'id';
    case CSRF_TOKEN = 'token';
    case NAME = 'name';
    case DESCRIPTION = 'description';
    case STEPS = 'steps';
    case INGREDIENTS = 'ingredients';
    case IMAGE = 'image';
    case IMAGE_REMOVE = 'image_remove';
    case PREPARATION_TIME = 'preparation_time';
    case CATEGORY = 'category';
    case PUBLIC = 'public';
    case SUBMIT = 'submit';

    public static function getNameWithForm(RECIPE_MODIFY_FORM_FIELDS $formField, bool $isArray = false): string
    {
        $fieldWithForm = self::FORM_NAME->value."[$formField->value]";

        if ($isArray) {
            $fieldWithForm .= '[]';
        }

        return $fieldWithForm;
    }
}
