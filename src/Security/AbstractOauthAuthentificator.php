<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\OauthRegistrationService;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractOauthAuthentificator extends OAuth2Authenticator
{

    use TargetPathTrait;
    protected string $serviceName = '';

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly RouterInterface $router,
        private readonly UserRepository $userRepository,
        private readonly OauthRegistrationService $registrationService
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return 'auth_oauth_check' === $request->attributes->get('_route') &&  $this->serviceName === $request->get('service');
    }

    public function onAuthenticationSuccess(Request $request,TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);
        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, \Exception $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        }
        return new RedirectResponse($this->router->generate('auth_oauth_login'));
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $credentials = $this->fetchAccessToken($this->getClient());
        $resourceOwner = $this->getRessourceOwnerFromCredentials($credentials);
        $user = $this->getUserFromResourceOwner($resourceOwner, $this->userRepository);

        if ($user === null) {
//            crée un user
            $this->registrationService->persist($resourceOwner);
        }

        return new SelfValidatingPassport(
                userBadge: new UserBadge($user->getUserIdentifier(), fn () => $user),
                badges: [
                    new RememberMeBadge()
            ]
        );
    }

    protected function getRessourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface
    {
        return $this->getClient()->fetchUserFromToken($credentials);

    }

    public function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient($this->serviceName);
    }

    abstract protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $repository): ?User;
}