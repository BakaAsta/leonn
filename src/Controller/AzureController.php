<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Psr\Log\LoggerInterface;

class AzureController extends AbstractController
{
    
    private $jwtManager;
    private $entityManager;
    private $userRepository;
    private $logger;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        LoggerInterface $logger
    ) {
        $this->jwtManager = $jwtManager;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    #[Route('/azure', name: 'app_azure_index')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('auth_oauth_login');
    }

    #[Route('/azure/auth', name: 'connect_azure_start')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('azure')
            ->redirect(['openid', 'profile', 'email'], []);
    }

    #[Route('/azureauth', name: 'connect_azure_check')]
    public function connectCheckAction(ClientRegistry $clientRegistry, SessionInterface $session, Security $security, UserPasswordHasherInterface $userPasswordHasher)
    {
        $client = $clientRegistry->getClient('azure');

        try {
            $userResponse = $client->fetchUser();
            $userArray = $userResponse->toArray();
            $email = $userArray['upn'];
            $userExist = $this->userRepository->findOneBy(['email' => $email]);

            $password = bin2hex(random_bytes(64));

            if (!$userExist) {
                $newUser = new User();
                $newUser->setEmail($email);
                $newUser->setPassword(
                    $userPasswordHasher->hashPassword(
                        $newUser,
                        $password
                    )
                );
                $newUser->setRoles(['ROLE_USER']);
                $this->entityManager->persist($newUser);
                $this->entityManager->flush();
                $userExist = $newUser;
                $this->entityManager->refresh($userExist);
            }

            $session->set('user_exist_id', $userExist->getId());

            $security->login($userExist, AppAuthenticator::class, 'main');
            

            $user = $userExist;

            $routeRedirection = 'app_produit';
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $routeRedirection = 'app_home';
            }

            return $this->redirectToRoute($routeRedirection);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}