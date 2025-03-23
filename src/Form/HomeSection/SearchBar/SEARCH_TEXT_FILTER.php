<?php

declare(strict_types=1);

namespace App\Form\HomeSection\SearchBar;

enum SEARCH_TEXT_FILTER: string
{
    case CONTAINS = 'contains';
    case EQUALS = 'equals';
    case STARTS_WITH = 'starts_with';
    case ENDS_WITH = 'ends_with';
}
