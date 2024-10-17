<?php

namespace App\Form;

use App\Entity\Pret;
use App\Entity\Produit;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use App\Form\EventSubscriber\UserSubscriber;
use App\Repository\ProduitRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PretType extends AbstractType
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('datePret', null, [
                'widget' => 'single_text',
            ])
            ->add('dateFin', null, [
                'widget' => 'single_text',
            ])
            ->add('commentaire')
            ->add('dateFinPrevue', null, [
                'widget' => 'single_text',
            ])
            // ->add('user', UserAutocompleteField::class, [
            // ])
            ->add('user', EntityType::class, [
                'required' => false,
                'class' => User::class,
                'autocomplete' => true,
                'tom_select_options' => [
                    'create'=> true
                ],
            ])
            ->addEventSubscriber(new UserSubscriber($this->entityManager, $this->passwordHasher))
            // ->add('produit', EntityType::class, [
            //     'class' => Produit::class,
            //     'required' => true,
            //     'placeholder' => 'Choisissez un produit',
            //     'choice_attr' => ['nom', 'refFabricant', 'refInterne'],
            //     'multiple' => true,
            //     'choice_label' => 'nom',
            //     'attr' => [
            //         'onkeydown' => 'return handleEnter(event);'
            //     ],
            //     'autocomplete' => true,
            //     'query_builder' => function (ProduitRepository $produitRepository) {
            //         return $produitRepository->createQueryBuilder('produit')
            //             ->leftJoin('produit.prets', 'pret')
            //             ->where('pret.id IS NULL OR pret.dateFin IS NOT NULL')
            //             ->andWhere('produit.rebus != :etat OR produit.rebus IS NULL')
            //             ->setParameter('etat', true);
            //     },
            // ])
            ->add('produit', ProduitAutocompleteField::class, [
            ])
            ->add('sauvegarder', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pret::class,
        ]);
    }
}
