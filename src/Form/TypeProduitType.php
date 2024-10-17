<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\TypeProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TypeProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => ['autofocus' => true],
            ])
            // on veut gérer l'affectation d'un produit a un type de produit
            // depuis le form de création d'un produit
            // ->add('produits', EntityType::class, [
            //     'class' => Produit::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
            ->add('savaugarder', SubmitType::class, [
                'label' => 'Sauvegarder',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TypeProduit::class,
        ]);
    }
}
