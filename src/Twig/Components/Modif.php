<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Modif
{
    public string $url;
    public string $id ;
    public ?string $color = '';

}
