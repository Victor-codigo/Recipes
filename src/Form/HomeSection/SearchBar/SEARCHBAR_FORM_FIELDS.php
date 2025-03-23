<?php

declare(strict_types=1);

namespace App\Form\HomeSection\SearchBar;

use App\Form\Common\FormFieldsNamesUtilTrait;

enum SEARCHBAR_FORM_FIELDS: string
{
    use FormFieldsNamesUtilTrait;

    case FORM_NAME = 'recipe_searchbar_form';
    case CSRF_TOKEN = 'token';
    case FIELD_FILTER = 'field_filter';
    case NAME_FILTER = 'name_filter';
    case SEARCH_VALUE = 'search_value';
    case SUBMIT = 'submit';
}
