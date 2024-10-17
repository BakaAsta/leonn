<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\StepForm\createUserFlow;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    // contruct
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }
    // #[IsGranted('ROLE_ADMIN')]
    #[Route('/', name: 'app_user')]
    public function index(SessionInterface $sessionInterface): Response
    {
        

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'titreBouton' => 'Ajouter un utilisateur',
            'titre' => 'Liste des utilisateurs',
            'accessToken' => $sessionInterface->get('accessToken')
        ]);
    }


    //supprimer un user
    // #[IsGranted('ROLE_ADMIN')]
    #[Route('/supprimer/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->get('_token')) && count($user->getPrets())==0) {
            $entityManager->remove($user);
            $entityManager->flush();

            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(2000) // 2 seconds
                ->addError('Utilisateur' . $user->getEmail() . ' a été supprimé avec succès');
        }
        return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
    }

    
    // ajout d'un user 
    #[Route('/ajouter', name: 'app_user_ajouter', methods: ['GET', 'POST'])]
    public function ajouter(Request $request, EntityManagerInterface $entityManager, createUserFlow $flow): Response
    {
        $user = new User();
        // $form = $this->createForm(UserType::class, $user);
        // $form->handleRequest($request);
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            "Password123"
        ));
        
        $user->setRoles(["ROLE_USER"]);

        $flow->isAllowDynamicStepNavigation(true);
        $flow->bind($user);
        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {

                $recupUser = $this->userRepository->findOneBy(['email' =>  $form->getData()->getEmail()]);

                if ($recupUser) {
                    notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(4000) // 2 seconds
                    ->addError('Cet utilisateur existe déjà');    
                    return $this->redirectToRoute('app_user_ajouter');
                }


                $entityManager->persist($user);
                $entityManager->flush();

                notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(4000) // 2 seconds
                    ->addSuccess('L\'utilisateur ' . $user->getEmail() . ' a été ajoutée');

                return $this->redirectToRoute('app_user');
            }
        }

        return $this->render('user/ajouter.html.twig', [
            'titre' => 'Ajouter un utilisateur ',
            'form' => $form->createView(),
            'flow' => $flow,
        ]);
    }
}
