<?php

namespace App\Form;

use App\Entity\Marque;
use App\Repository\MarqueRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class MarqueAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Marque::class,
            'placeholder' => 'Choisissez une marque',
            'multiple' => false,
            'choice_label' => 'nom',
            'searchable_fields' => ['nom'],
            'tom_select_options' => [
                'create' => true,
            ],


            // 'query_builder' => function (MarqueRepository $marqueRepository) {
            //     return $marqueRepository->createQueryBuilder('marque');
            // },
            // 'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
