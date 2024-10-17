<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {

        if ($this->getUser()) {
            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(2000) // 2 seconds
                ->addInfo('Vous êtes déjà inscrit sur Leonn');

            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            

            $entityManager->persist($user);
            $entityManager->flush();

            

            // do anything else you need here, like send an email
            notyf()->position('x', 'center')->position('y', 'top')->duration(4000)->addInfo('Utilisateur ' . $user->getEmail() .  ' enregistré avec succès');
            return $security->login($user, AppAuthenticator::class, 'main');
        }
        
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
