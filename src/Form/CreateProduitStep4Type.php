<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\TypeProduit;
use App\Repository\MarqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateProduitStep4Type extends AbstractType
{
    public function __construct(
    ) {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('quantite', IntegerType::class, [
            'required' => true,
            'attr' => ['autofocus' => true],
        ]);       
    }
    
    public function getBlockPrefix() {
		return 'CreateProduitStep4';
	}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}