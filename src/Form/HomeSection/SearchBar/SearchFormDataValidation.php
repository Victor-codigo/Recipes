<?php

declare(strict_types=1);

namespace App\Form\HomeSection\SearchBar;

use Symfony\Component\Validator\Constraints as Assert;

class SearchFormDataValidation
{
    #[Assert\Choice(
        callback: [FIELD_FILTERS::class, 'cases'],
        multiple: false,
    )]
    public FIELD_FILTERS $field_filter;

    #[Assert\Choice(
        callback: [SEARCH_TEXT_FILTER::class, 'cases'],
        multiple: false,
    )]
    public SEARCH_TEXT_FILTER $name_filter;

    public string $search_value;
}
