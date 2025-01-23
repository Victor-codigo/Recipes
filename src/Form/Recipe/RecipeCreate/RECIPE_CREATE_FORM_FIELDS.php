<?php

declare(strict_types=1);

namespace App\Form\Recipe\RecipeCreate;

enum RECIPE_CREATE_FORM_FIELDS: string
{
    case FORM_NAME = 'recipe_create_form';
    case CSRF_TOKEN = 'recipe_create_form[token]';
    case NAME = 'recipe_create_form[name]';
    case DESCRIPTION = 'recipe_create_form[description]';
    case STEPS = 'recipe_create_form[steps][]';
    case INGREDIENTS = 'recipe_create_form[ingredients][]';
    case IMAGE = 'recipe_create_form[image]';
    case PREPARATION_TIME = 'recipe_create_form[preparation_time]';
    case CATEGORY = 'recipe_create_form[category]';
    case SUBMIT = 'recipe_create_form[submit]';
}
