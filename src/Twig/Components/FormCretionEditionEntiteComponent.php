<?php

namespace App\Twig\Components;

use App\Entity\Carte;
use Craue\FormFlowBundle\Form\FormFlow;
use Symfony\Component\Form\FormView;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class FormCretionEditionEntiteComponent
{
    public FormView $form;

    public FormFlow $flow;

    public ?string $typeForm;

    public ?string $quantiteActuelle;

    public ?string $route;

    public ?Carte $carte;
}
