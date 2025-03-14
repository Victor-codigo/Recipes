<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeRemove;

use App\Form\Common\FormFieldsNamesUtilTrait;

enum RECIPE_REMOVE_FORM_FIELDS: string
{
    use FormFieldsNamesUtilTrait;

    case FORM_NAME = 'recipe_remove_form';
    case RECIPES_ID = 'recipes_id';
    case CSRF_TOKEN = 'token';
    case SUBMIT = 'submit';
}
