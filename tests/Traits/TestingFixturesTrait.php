<?php

declare(strict_types=1);

namespace App\Tests\Traits;

trait TestingFixturesTrait
{
    protected const string RECIPES_FIXTURES_PATH = 'tests/Fixtures/Database/Recipes.yml';
    protected const string DATETIME_FIXTURES_PATH = 'tests/Fixtures/Database/DateTime.yml';
    protected const string USERS_FIXTURES_PATH = 'tests/Fixtures/Database/Users.yml';

    protected const string USER_1_FIXTURES_ID = 'a9fee148-7b07-4c3e-8f8c-3cf8225a9bf6';
    protected const string RECIPE_1_FIXTURES_ID = 'a5b729af-30a5-4a9d-aa62-2ead180cc204';
    protected const string RECIPE_WITH_GROUP_FIXTURES_ID = '24778697-696c-4466-bd3d-404bbe000ed0';
}
