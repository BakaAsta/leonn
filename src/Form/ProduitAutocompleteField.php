<?php

namespace App\Form;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;


#[AsEntityAutocompleteField]
class ProduitAutocompleteField extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => Produit::class,
            'placeholder' => 'Choisissez un produit',
            'searchable_fields' => ['nom', 'refFabricant', 'refInterne'],
            'multiple' => true,
            'choice_label' => 'nom',
            'attr' => [
                    'onkeydown' => 'return handleEnter(event);'
            ],
            // je dois récupérer les produit qui ne sont pas affecté à un pret et qui ne sont pas au rebus 
            'query_builder' => function (ProduitRepository $produitRepository) {
                return $produitRepository->createQueryBuilder('produit')
                    ->leftJoin('produit.prets', 'pret')
                    ->where('pret.id IS NULL OR pret.dateFin IS NOT NULL')
                    ->andWhere('produit.etat != :etat AND produit.etat != :etat2')
                    ->setParameter('etat', 'rebus')
                    ->setParameter('etat2', 'pret');
            },


            // 'query_builder' => function (ProduitRepository $produitRepository) {
            //     return $produitRepository->createQueryBuilder('produit');
            // },
            // 'security' => 'ROLE_SOMETHING',
        ]);
    }

    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
