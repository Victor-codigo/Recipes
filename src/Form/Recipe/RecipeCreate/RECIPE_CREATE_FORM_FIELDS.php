<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeCreate;

use App\Form\Common\FormFieldsNamesUtilTrait;

enum RECIPE_CREATE_FORM_FIELDS: string
{
    use FormFieldsNamesUtilTrait;

    case FORM_NAME = 'recipe_create_form';
    case CSRF_TOKEN = 'token';
    case NAME = 'name';
    case DESCRIPTION = 'description';
    case STEPS = 'steps';
    case INGREDIENTS = 'ingredients';
    case IMAGE = 'image';
    case PREPARATION_TIME = 'preparation_time';
    case CATEGORY = 'category';
    case PUBLIC = 'public';
    case SUBMIT = 'submit';
}
