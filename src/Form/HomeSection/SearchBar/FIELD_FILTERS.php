<?php

declare(strict_types=1);

namespace App\Form\HomeSection\SearchBar;

enum FIELD_FILTERS: string
{
    case NAME = 'name';
    case CATEGORY = 'category';
    case USER_NAME = 'user_name';
}
