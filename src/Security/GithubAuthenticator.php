<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class GithubAuthenticator extends AbstractOauthAuthentificator
{
    protected string $serviceName = 'github';

        public function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $repository): ?User
        {
            if (!$resourceOwner instanceof GithubResourceOwner) {
                throw new \InvalidArgumentException('expecting github user');
            }
            if (empty($resourceOwner->toArray()['email'])) {
                throw new AuthenticationException("email not available");
            }
            return $repository->findOneBy(
                [
                    'github_id' => $resourceOwner->getId(),
                    'email' => $resourceOwner->getEmail(),
                ]);
        }
}