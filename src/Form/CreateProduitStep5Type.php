<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateProduitStep5Type extends AbstractType
{
    public function __construct(
    ) { 
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('refFabricant', TextType::class, [
            'required' => false,
            'attr' => [
                'autofocus' => true,
            ]
            // 'attr' => ['autofocus' => true, 'class' => 'relative bg-gray-50ring-0 outline-none border border-neutral-500 text-neutral-900 placeholder-violet-700 text-sm rounded-lg focus:ring-violet-500  focus:border-violet-500 block w-64 p-2.5 checked:bg-emerald-500']
        ]);
        // ->add('sauvegarder', SubmitType::class, [
        //     'label' => 'Scannez Moi',
        //     'attr' => ['class' => 'comic-button inline-block px-5 py-2.5 text-2xl font-bold text-center text-white bg-purple-500 border-2 border-black rounded-lg shadow-shadow-custom hover:shadow-shadow-hover hover:bg-white hover:text-purple-500 hover:border-purple-500 active:shadow-none active:translate-y-1 transition-all duration-300 ease-in-out cursor-pointer']
        // ]);    
    }
    

    public function getBlockPrefix() {
		return 'CreateProduitStep5';
	}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}