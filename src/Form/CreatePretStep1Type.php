<?php

namespace App\Form;

use App\Entity\Pret;
use App\Entity\Produit;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreatePretStep1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
        
            $form->add('produit', EntityType::class, [
                'class' => Produit::class,
                'required' => true,
                'placeholder' => 'Choisissez un produit',
                'choice_attr' => function (Produit $produit) {
                    return [
                        'data-nom' => $produit->getNom(),
                        'data-refFabricant' => $produit->getRefFabricant(),
                        'data-refInterne' => $produit->getRefInterne(),
                    ];
                },
                'attr' => [
                    'id' => 'produit-autocomplete', // Ajoutez cet attribut
                ],
                'multiple' => true,
                'choice_label' => 'nom',
                'autocomplete' => true,
                'query_builder' => function (EntityRepository $er) use ($data) {
                    $qb = $er->createQueryBuilder('p');
                    $qb->join('p.typeProduit', 't')
                    ->where('p.etat = :etat')
                    ->andWhere('t.nom != :type')
                    ->setParameter('etat', 'Disponible')
                    ->setParameter('type', 'Immo');
                    
                    if ($data && $data->getProduit()) {
                        $produitsIds = array_map(function($produit) {
                            return $produit->getId();
                        }, $data->getProduit()->toArray());

                        if ($produitsIds) {
                            $qb->orWhere($qb->expr()->in('p.id', $produitsIds));
                        }
                    }

                    return $qb;
                },


            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pret::class,
        ]);
    }
}
