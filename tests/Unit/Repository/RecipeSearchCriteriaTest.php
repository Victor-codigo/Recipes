<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repository;

use App\Common\RECIPE_TYPE;
use App\Form\HomeSection\SearchBar\SEARCH_TEXT_FILTER;
use App\Repository\RecipeSearchCriteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Order;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RecipeSearchCriteriaTest extends TestCase
{
    private RecipeSearchCriteria $object;

    protected function setUp(): void
    {
        parent::setUp();

        $this->object = new RecipeSearchCriteria();
    }

    #[Test]
    public function itShouldAddNameToCriteria(): void
    {
        $name = 'value';
        $textFilter = SEARCH_TEXT_FILTER::EQUALS;

        $return = $this->object->addName($name, $textFilter);

        /** @var Comparison */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertEquals('name', $comparison->getField());
        self::assertEquals($name, $comparison->getValue()->getValue());
        self::assertEquals('=', $comparison->getOperator());
    }

    #[Test]
    public function itShouldAddNameNullToCriteria(): void
    {
        $name = null;
        $textFilter = SEARCH_TEXT_FILTER::EQUALS;

        $return = $this->object->addName($name, $textFilter);

        /** @var Comparison|null */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertNull($comparison);
    }

    #[Test]
    public function itShouldAddUserIdToCriteria(): void
    {
        $userId = 'user id';

        $return = $this->object->addUserId($userId);

        /** @var Comparison */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertEquals('userId', $comparison->getField());
        self::assertEquals($userId, $comparison->getValue()->getValue());
        self::assertEquals('=', $comparison->getOperator());
    }

    #[Test]
    public function itShouldAddUserIdNullToCriteria(): void
    {
        $userId = null;

        $return = $this->object->addUserId($userId);

        /** @var Comparison|null */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertNull($comparison);
    }

    #[Test]
    public function itShouldAddUserNameToCriteria(): void
    {
        $name = 'value';
        $textFilter = SEARCH_TEXT_FILTER::EQUALS;

        $return = $this->object->addUserName($name, $textFilter);

        /** @var Comparison */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertEquals('user.name', $comparison->getField());
        self::assertEquals($name, $comparison->getValue()->getValue());
        self::assertEquals('=', $comparison->getOperator());
    }

    #[Test]
    public function itShouldAddUserNameNullToCriteria(): void
    {
        $name = null;
        $textFilter = SEARCH_TEXT_FILTER::EQUALS;

        $return = $this->object->addUserName($name, $textFilter);

        /** @var Comparison|null */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertNull($comparison);
    }

    #[Test]
    public function itShouldAddGroupIdToCriteria(): void
    {
        $groupId = 'group id';

        $return = $this->object->addGroupId($groupId);

        /** @var Comparison */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertEquals('groupId', $comparison->getField());
        self::assertEquals($groupId, $comparison->getValue()->getValue());
        self::assertEquals('=', $comparison->getOperator());
    }

    #[Test]
    public function itShouldAddGroupIdNullToCriteria(): void
    {
        $groupId = null;

        $return = $this->object->addGroupId($groupId);

        /** @var Comparison|null */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertNull($comparison);
    }

    #[Test]
    public function itShouldAddCategoryToCriteria(): void
    {
        $category = RECIPE_TYPE::DESSERT;

        $return = $this->object->addCategory($category);

        /** @var Comparison */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertEquals('category', $comparison->getField());
        self::assertEquals($category->value, $comparison->getValue()->getValue());
        self::assertEquals('=', $comparison->getOperator());
    }

    #[Test]
    public function itShouldAddCategoryNoCategoryToCriteria(): void
    {
        $category = RECIPE_TYPE::NO_CATEGORY;

        $return = $this->object->addCategory($category);

        /** @var Comparison */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertEquals('category', $comparison->getField());
        self::assertNull($comparison->getValue()->getValue());
        self::assertEquals('=', $comparison->getOperator());
    }

    #[Test]
    public function itShouldAddCategoryNullToCriteria(): void
    {
        $category = null;

        $return = $this->object->addCategory($category);

        /** @var Comparison|null */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertNull($comparison);
    }

    #[Test]
    public function itShouldAddPublicToCriteria(): void
    {
        $public = true;

        $return = $this->object->addPublic($public);

        /** @var Comparison */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertEquals('public', $comparison->getField());
        self::assertTrue($comparison->getValue()->getValue());
        self::assertEquals('=', $comparison->getOperator());
    }

    #[Test]
    public function itShouldAddPublicNullToCriteria(): void
    {
        $public = null;

        $return = $this->object->addPublic($public);

        /** @var Comparison|null */
        $comparison = $this->object->criteria->getWhereExpression();
        self::assertEquals($this->object, $return);
        self::assertNull($comparison);
    }

    #[Test]
    public function itShouldSetPaginationToCriteria(): void
    {
        $page = 3;
        $pageItems = 10;

        $return = $this->object->setPagination($page, $pageItems);

        self::assertEquals($this->object, $return);
        self::assertEquals(20, $this->object->criteria->getFirstResult());
        self::assertEquals($pageItems, $this->object->criteria->getMaxResults());
    }

    #[Test]
    public function itShouldSetOrderByNameCriteria(): void
    {
        $return = $this->object->setOrderByRecipeName();

        self::assertEquals($this->object, $return);
        self::assertEquals(['name' => Order::Ascending], $this->object->criteria->orderings());
    }
}
