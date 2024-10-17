<?php

namespace App\Form;

use App\Entity\Carte;
use App\Repository\CarteRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class CarteAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Carte::class,
            'placeholder' => 'Choisissez une carte',
            'searchable_fields' => ['reference'],
            'choice_label' => 'reference',

            // 'query_builder' => function (CarteRepository $carteRepository) {
            //     return $carteRepository->createQueryBuilder('carte');
            // },
            // 'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
