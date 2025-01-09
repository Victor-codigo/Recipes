<?php

declare(strict_types=1);

namespace App\Common\DtoBuilder;

interface DtoBuilderInterface
{
    public function build(): self;
}
