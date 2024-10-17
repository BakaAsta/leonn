<?php

namespace App\Form;

use App\Entity\TypeProduit;
use App\Repository\TypeProduitRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
class TypeProduitAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => TypeProduit::class,
            'placeholder' => 'Choisissez un type de produit',
            'multiple' => true,
            'searchable_fields' => ['nom'],
            'choice_label' => 'nom',


            // 'query_builder' => function (TypeProduitRepository $typeProduitRepository) {
            //     return $typeProduitRepository->createQueryBuilder('typeProduit');
            // },
            // 'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
