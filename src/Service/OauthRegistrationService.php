<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

readonly class OauthRegistrationService
{

    public function __construct(
        private UserRepository $repository
    )
    {
    }

    public function persist(ResourceOwnerInterface $resourceOwner): User
    {
        $user = match (true) {
            $resourceOwner instanceof GoogleUser => (new User())
            ->setEmail($resourceOwner->getEmail())
            ->setGoogleId($resourceOwner->getId()),
//            mon user ne match pas avec une githubUser
            $resourceOwner instanceof GithubResourceOwner => (new User())
            ->setEmail($resourceOwner->getEmail())
            ->setGithubId($resourceOwner->getId()),
        };
        $this->repository->addUser($user, true);
        return $user;
    }
}