<?php

namespace App\Twig\Components;

use phpDocumentor\Reflection\Types\Boolean;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class MessageFlash
{
    public ?string $type = '';
    public ?string $message = '';
}
