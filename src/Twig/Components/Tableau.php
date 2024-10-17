<?php

namespace App\Twig\Components;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class Tableau
{
    public string $entityType;
    public string $titre;
    public ?string $titreBouton;
    public ?string $actionButton;
    public array $listeTh;
    public array $listeAttributs;
    public $listeObjets;
    public ?string $urlModif;
    public ?string $urlSupp;
}
