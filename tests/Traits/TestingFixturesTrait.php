<?php

declare(strict_types=1);

namespace App\Tests\Traits;

trait TestingFixturesTrait
{
    protected const string RECIPES_FIXTURES_PATH = 'tests/Fixtures/Database/Recipes.yml';
    protected const string DATETIME_FIXTURES_PATH = 'tests/Fixtures/Database/DateTime.yml';
    protected const string USERS_FIXTURES_PATH = 'tests/Fixtures/Database/Users.yml';

    protected const string USER_1_FIXTURES_ID = 'a9fee148-7b07-4c3e-8f8c-3cf8225a9bf6';
}
