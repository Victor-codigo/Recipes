<?php

declare(strict_types=1);

namespace App\Templates\Components\HomeSection\SearchBar;

use Common\Domain\DtoBuilder\DtoBuilder;

class SearchBarComponentLangDto
{
    public DtoBuilder $builder;

    public readonly string $searchLabel;
    public readonly string $searchPlaceholder;
    public readonly string $searchButton;
    public readonly string $sectionFilterLabel;
    public readonly string $textFilterLabel;
    public readonly string $inputTextFilterLabel;
    /**
     * @var string[]
     */
    public readonly array $sectionFilters;
    public readonly array $filters;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'searchInput',
            'sectionFilters',
            'nameFilters',
        ]);
    }

    public function input(string $searchLabel, string $searchPlaceholder, string $searchButton, string $sectionFilterLabel, string $textFilterLabel, string $inputTextFilterLabel): self
    {
        $this->builder->setMethodStatus('searchInput', true);

        $this->searchLabel = $searchLabel;
        $this->searchPlaceholder = $searchPlaceholder;
        $this->searchButton = $searchButton;
        $this->sectionFilterLabel = $sectionFilterLabel;
        $this->textFilterLabel = $textFilterLabel;
        $this->inputTextFilterLabel = $inputTextFilterLabel;

        return $this;
    }

    /**
     * @param string[] $filters
     */
    public function nameFilters(array $filters): self
    {
        $this->builder->setMethodStatus('nameFilters', true);

        $this->filters = $filters;

        return $this;
    }

    /**
     * @param string[] $sectionFilters
     */
    public function sectionFilters(array $sectionFilters): self
    {
        $this->builder->setMethodStatus('sectionFilters', true);

        $this->sectionFilters = $sectionFilters;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
