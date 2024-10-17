<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class HomeCard
{
    public ?string $link;
    public string $color;
    public string $title;
    public string $value;
    public ?string $badge;
}
