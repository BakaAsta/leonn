<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitQuantiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite', IntegerType::class, [
            'attr' => [
                'autofocus' => true,
            ]
            ]);
        // ->add('sauvegarder', SubmitType::class, [
        //     'label' => 'Scannez Moi',
        //     'attr' => ['class' => 'comic-button inline-block px-5 py-2.5 text-2xl font-bold text-center text-white bg-purple-500 border-2 border-black rounded-lg shadow-shadow-custom hover:shadow-shadow-hover hover:bg-white hover:text-purple-500 hover:border-purple-500 active:shadow-none active:translate-y-1 transition-all duration-300 ease-in-out cursor-pointer']
        // ]);
    }

    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'data_class' => Produit::class
        ]);
    }
}
