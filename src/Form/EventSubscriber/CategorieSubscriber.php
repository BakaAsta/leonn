<?php

namespace App\Form\EventSubscriber;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CategorieSubscriber implements EventSubscriberInterface
{

    public function __construct(private EntityManagerInterface $categorieManager)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (!$data || !isset($data['categories']) || !is_array($data['categories'])) {
            return;
        }

        $categorieNames = $data['categories'];
        $categoryIds = [];

        foreach ($categorieNames as $cat) {
            if (is_numeric($cat)) {
                // Si l'ID de catégorie est déjà celui d'une catégorie existante
                $category = $this->categorieManager->getRepository(Categorie::class)->find($cat);
                if ($category) {
                    $categoryIds[] = $category->getId();
                    continue;
                }
            }

            // Rechercher la catégorie par nom ou créer une nouvelle catégorie si elle n'existe pas
            $category = $this->categorieManager->getRepository(Categorie::class)->findOneBy(['nom' => $cat]);

            if (!$category) {
                $category = new Categorie();
                $category->setNom($cat);
                $this->categorieManager->persist($category);
                $this->categorieManager->flush();
            }

            $categoryIds[] = $category->getId();
        }

        // Remplacer les noms/données de catégories par les ID
        $data['categories'] = $categoryIds;
        $event->setData($data);
    }
}