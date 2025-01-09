<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\SearchBar;

enum SECTION_FILTERS: string
{
    case PRODUCT = 'product';
    case SHOP = 'shop';
    case ORDER = 'order';
    case LIST_ORDERS = 'list_orders';
    case GROUP = 'group';
    case GROUP_USERS = 'group_users';
}
