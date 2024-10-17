<?php

namespace App\Form;
use App\Form\EventSubscriber\MarqueSubscriber;
use App\Entity\Categorie;
use App\Entity\Marque;
use App\Entity\Produit;
use App\Entity\TypeProduit;
use App\Form\EventSubscriber\CategorieSubscriber;
use App\Repository\MarqueRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ProduitType extends AbstractType
{

    public function __construct(
        private MarqueRepository $marqueRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            // ->add('refInterne')
            ->add('refFabricant')
            ->add('commentaire')
            // ->add('rebus', null, [
            //     'attr' => ['class' => 'form-checkbox'],
            // ])
            ->add('dateRebus', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-input']
            ])
            ->add('createAt', DateTimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'hidden']
            ])
            ->add('updatedAt', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'hidden']
            ])
            ->add('typeProduit', EntityType::class, [
                // 'attr' => ['class' => 'form-checkbox'],
                'class' => TypeProduit::class,
                'choice_label' => 'nom',
                'expanded' => true,
                'multiple' => true,
            ])
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
            
            ->add('sauvegarder', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
