<?php

declare(strict_types=1);

namespace App\Common;

enum RECIPE_TYPE: string
{
    case NO_CATEGORY = 'NO_CATEGORY';
    case BREAKFAST = 'BREAKFAST';
    case BRUNCH = 'BRUNCH';
    case LUNCH = 'LUNCH';
    case DINNER = 'DINNER';
    case DESSERT = 'DESSERT';
    case SANDWICH = 'SANDWICH';
    case APPETISER = 'APPETISER';
    case SOUP = 'SOUP';
    case SALAD = 'SALAD';
    case SNACK = 'SNACK';
    case BURGER = 'BURGER';
    case PIZZA = 'PIZZA';
    case CAKE = 'CAKE';
    case SEAFOOD = 'SEAFOOD';
    case RICE = 'RICE';
    case PASTA = 'PASTA';
    case ICE_CREAM = 'ICE_CREAM';
    case MEAT = 'MEAT';
}
