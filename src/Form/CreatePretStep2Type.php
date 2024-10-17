<?php

namespace App\Form;

use App\Entity\Pret;
use App\Entity\User;
use App\Form\EventSubscriber\UserSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreatePretStep2Type extends AbstractType
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'autocomplete' => true,
                'tom_select_options' => [
                    'create'=> true,
                ],
                // lance une fonction quand la personne fais entrer 
                'attr' => [
                    'id' => 'user-autocomplete', // Ajoutez cet attribut
                ],
            ])
            ->addEventSubscriber(new UserSubscriber($this->entityManager, $this->passwordHasher));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
