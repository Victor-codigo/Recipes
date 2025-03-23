<?php

declare(strict_types=1);

namespace App\Repository;

use App\Common\RECIPE_TYPE;
use App\Form\HomeSection\SearchBar\SEARCH_TEXT_FILTER;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Order;

class RecipeSearchCriteria
{
    public readonly Criteria $criteria;

    public function __construct()
    {
        $this->criteria = Criteria::create();
    }

    public function addName(?string $name, SEARCH_TEXT_FILTER $textFilter): self
    {
        if (null === $name) {
            return $this;
        }

        $this->criteria->andWhere($this->createTextFilter('name', $name, $textFilter));

        return $this;
    }

    public function addUserId(?string $userId): self
    {
        if (null === $userId) {
            return $this;
        }

        $this->criteria->andWhere($this->criteria->expr()->eq('userId', $userId));

        return $this;
    }

    public function addUserName(?string $userName, SEARCH_TEXT_FILTER $textFilter): self
    {
        if (null === $userName) {
            return $this;
        }

        $this->criteria->andWhere($this->createTextFilter('user.name', $userName, $textFilter));

        return $this;
    }

    public function addGroupId(?string $groupId): self
    {
        if (null === $groupId) {
            return $this;
        }

        $this->criteria->andWhere($this->criteria->expr()->eq('groupId', $groupId));

        return $this;
    }

    public function addCategory(?RECIPE_TYPE $category): self
    {
        if (null === $category) {
            return $this;
        }

        $categoryValue = $category->value;
        if (RECIPE_TYPE::NO_CATEGORY === $category) {
            $categoryValue = null;
        }

        $this->criteria->andWhere($this->createTextFilter('category', $categoryValue, SEARCH_TEXT_FILTER::EQUALS));

        return $this;
    }

    public function addPublic(?bool $public): self
    {
        if (null === $public) {
            return $this;
        }

        $this->criteria->andWhere($this->criteria->expr()->eq('public', $public));

        return $this;
    }

    public function setPagination(int $page, int $pageItems): self
    {
        $this->criteria->setFirstResult(($page - 1) * $pageItems);
        $this->criteria->setMaxResults($pageItems);

        return $this;
    }

    public function setOrderByRecipeName(): self
    {
        $this->criteria->orderBy(['name' => Order::Ascending]);

        return $this;
    }

    private function createTextFilter(string $field, mixed $value, SEARCH_TEXT_FILTER $textFilter): Comparison
    {
        $expressionBuilder = $this->criteria->expr();

        return match ($textFilter) {
            SEARCH_TEXT_FILTER::EQUALS => $expressionBuilder->eq($field, $value),
            SEARCH_TEXT_FILTER::CONTAINS => $expressionBuilder->contains($field, $value),
            SEARCH_TEXT_FILTER::STARTS_WITH => $expressionBuilder->startsWith($field, $value),
            SEARCH_TEXT_FILTER::ENDS_WITH => $expressionBuilder->endsWith($field, $value),
        };
    }
}
