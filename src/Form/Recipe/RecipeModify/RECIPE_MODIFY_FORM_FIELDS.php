<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeModify;

use App\Form\Common\FormFieldsNamesUtilTrait;

enum RECIPE_MODIFY_FORM_FIELDS: string
{
    use FormFieldsNamesUtilTrait;

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
}
