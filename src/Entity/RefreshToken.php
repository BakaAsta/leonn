<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;


/**
 * @ORM\Entity
 * @ORM\Table("refresh_tokens")
 */

 #[ApiResource(
    operations: [
        new Post(normalizationContext: ['groups' => 'conference:list'])
    ],
)]
#[ORM\Entity]
#[ORM\Table(name: 'refresh_tokens')]
class RefreshToken extends BaseRefreshToken
{   
}