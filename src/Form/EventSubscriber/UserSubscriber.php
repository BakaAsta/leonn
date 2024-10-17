<?php 
namespace App\Form\EventSubscriber;

use App\Entity\User;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(private 
    EntityManagerInterface $entityManager, 
    private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSubmit(PreSubmitEvent $event)
    {
        $data = $event->getData();
        if (!$data || !isset($data['user'])) {
            return;
        }

        $userEmail = $data['user'];

        // Assurez-vous que $userEmail est un email valide, pas un ID
        if (is_numeric($userEmail)) {
            $user = $this->entityManager->getRepository(User::class)->find($userEmail);
        } else {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userEmail]);
        }

        if (!$user && filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            // Créer un nouvel utilisateur si il n'existe pas et que l'email est valide
            $user = new User();
            $user->setEmail($userEmail);
            // Définir un mot de passe par défaut, vous pouvez adapter cela selon vos besoins
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                "Password123"
            ));
            $user->setRoles(["ROLE_USER"]);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        // Mettre à jour les données avec l'ID de l'utilisateur
        $data['user'] = $user->getId();
        $event->setData($data);
    }
}