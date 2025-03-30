<?php

declare(strict_types=1);

namespace App\Templates\Components\Rating;

use App\Templates\Components\TwigComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'RatingComponent',
    template: 'Components/Rating/RatingComponent.html.twig'
)]
class RatingComponent extends TwigComponent
{
    public int $rating;

    protected static function getComponentName(): string
    {
        return 'RatingComponent';
    }

    public function mount(?RatingComponentDto $data = null): void
    {
        if (null === $data) {
            $this->rating = 0;

            return;
        }

        $this->rating = $data->rating;
    }
}
