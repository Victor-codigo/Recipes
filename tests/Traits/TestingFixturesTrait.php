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
    protected const string RECIPE_2_FIXTURES_ID = '416bbe7c-51ab-4a55-b981-f58aa6412410';
    protected const string RECIPE_4_FIXTURES_ID = '65543f88-cfcc-4bb8-a3a7-aa6169bcaea8';
    protected const string RECIPE_GROUP_ID_FIXTURES_ID = '8ff7041c-85b8-4f42-b4ce-4935f7fb5d73';

    protected const string RECIPE_WITH_GROUP_FIXTURES_ID = '24778697-696c-4466-bd3d-404bbe000ed0';
}
