<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Marque;
use App\Entity\Produit;
use App\Form\EventSubscriber\CategorieSubscriber;
use App\Form\EventSubscriber\MarqueSubscriber;
use App\Repository\CategorieRepository;
use App\Repository\MarqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateProduitStep3Type extends AbstractType
{
    public function __construct(
        private MarqueRepository $marqueRepository,
        private CategorieRepository $categorieRepository,
        private EntityManagerInterface $entityManager,
    ) {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('marque', EntityType::class, [
            'required' => false,
            'class' => Marque::class,
            'autocomplete' => true,
            'tom_select_options' => [
                // 'persist'=> false,
                // 'createOnBlur'=> true,
                'create'=> true
            ],
        ])
        ->addEventSubscriber(new MarqueSubscriber($this->entityManager))
        ->add('categories', EntityType::class, [
            'required' => true,
            'class' => Categorie::class,
            'autocomplete' => true,
            'multiple' => true,
            'tom_select_options' => [
                'create'=> true
            ],
        ])
        ->addEventSubscriber(new CategorieSubscriber($this->entityManager))
        ->getForm();
    }
    
    

    public function getBlockPrefix() {
		return 'CreateProduitStep3';
	}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}