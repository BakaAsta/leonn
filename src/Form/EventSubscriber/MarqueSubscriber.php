<?php 
namespace App\Form\EventSubscriber;

use App\Entity\Marque;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MarqueSubscriber implements EventSubscriberInterface {
   

    public function __construct(private EntityManagerInterface $entityManager) {
    }

    public static function getSubscribedEvents() {
        return [
            // Vous pouvez aussi écouter l'événement FormEvents::SUBMIT si nécessaire
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSubmit(FormEvent $event) {
        $data = $event->getData();
        if (!$data || !isset($data['marque'])) {
            return;
        }
    
        $marqueName = $data['marque'];
    
        // Assurez-vous que $marqueName est un nom valide, pas un ID
        if (is_numeric($marqueName)) {
            $marque = $this->entityManager->getRepository(Marque::class)->find($marqueName);
        } else {
            $marque = $this->entityManager->getRepository(Marque::class)->findOneBy(['nom' => $marqueName]);
        }
    
        if (!$marque && !is_numeric($marqueName)) {
            // Créer une nouvelle marque si elle n'existe pas encore et que le nom est valide
            $marque = new Marque();
            $marque->setNom($marqueName);
            $this->entityManager->persist($marque);
            $this->entityManager->flush();
        }
    
        // Mettre à jour les données avec l'ID de la marque
        $data['marque'] = $marque->getId();
        $event->setData($data);
    }    
}