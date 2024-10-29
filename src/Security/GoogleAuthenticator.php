<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use http\Exception\RuntimeException;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * @see https://symfony.com/doc/current/security/custom_authenticator.html
 */
class GoogleAuthenticator extends AbstractOauthAuthentificator
{
    protected string $serviceName = 'google';

    protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $repository): ?User
    {
        if (!$resourceOwner instanceof GoogleUser) {
            throw new RuntimeException("expecting google user");
        }
        if (true !== ($resourceOwner->toArray()['email_verified'] ?? null)) {
            throw new AuthenticationException("email not verified");
        }
        return $repository->findOneBy(
            [
                'google_id' => $resourceOwner->getId(),
                'email' => $resourceOwner->getEmail(),
            ]);
    }
}
