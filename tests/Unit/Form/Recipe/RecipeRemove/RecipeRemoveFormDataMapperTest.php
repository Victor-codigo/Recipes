<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form\Recipe\RecipeRemove;

use App\Form\Recipe\RecipeRemove\RecipeRemoveFormDataMapper;
use App\Form\Recipe\RecipeRemove\RecipeRemoveFormDataValidation;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RecipeRemoveFormDataMapperTest extends TestCase
{
    private RecipeRemoveFormDataMapper $object;

    protected function setUp(): void
    {
        parent::setUp();

        $this->object = new RecipeRemoveFormDataMapper();
    }

    #[Test]
    public function mapRecipeRemoveFormDataValidationIntoARecipesIdCollection(): void
    {
        $formData = new RecipeRemoveFormDataValidation();
        $formData->recipes_id = [
            'recipe id 1',
            'recipe id 2',
            'recipe id 3',
        ];
        $collectionExpected = new ArrayCollection([
            'recipe id 1',
            'recipe id 2',
            'recipe id 3',
        ]);

        $return = $this->object->toRecipesIdCollection($formData);

        $this->assertEquals($collectionExpected, $return);
    }
}
