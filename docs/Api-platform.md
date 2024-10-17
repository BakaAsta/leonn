# API Platform JWT Auth

Ce guide vous explique comment configurer l'authentification JWT dans un projet Symfony utilisant API Platform.

## Pré-requis

1. **Symfony** : Assurez-vous que Symfony est installé dans votre projet.
2. **API Platform** : Installez API Platform dans votre projet Symfony.

## Étapes de configuration

### 1. Installer LexikJWTAuthenticationBundle

Installez le bundle LexikJWTAuthenticationBundle via Composer :

```bash
composer require "lexik/jwt-authentication-bundle"
```

### 2. Générer la paire de clés JWT

Installez OpenSSL si ce n'est pas déjà fait, puis générez la paire de clés JWT :

```bash
sudo apt-get install openssl

php bin/console lexik:jwt:generate-keypair
```

### 3. Configurer les permissions des clés

Assurez-vous que les fichiers de clés sont accessibles par l'utilisateur approprié :

```bash
setfacl -R -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
setfacl -dR -m u:www-data:rX -m u:"$(whoami)":rwX config/jwt
```

### 4. Configurer la sécurité

Ajoutez les configurations suivantes dans config/packages/security.yaml :

```yaml
security:
    encoders:
        App\Entity\User:
            algorithm: bcrypt

    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    firewalls:
        api:
            pattern: ^/api/
            stateless: true
            provider: app_user_provider
            jwt: ~

        main:
            anonymous: true
            json_login:
                check_path: /auth
                username_path: email
                password_path: password
                success_handler: lexik_jwt_authentication.handler.authentication_success
                failure_handler: lexik_jwt_authentication.handler.authentication_failure

    access_control:
        - { path: ^/$, roles: PUBLIC_ACCESS }
        - { path: ^/docs, roles: PUBLIC_ACCESS }
        - { path: ^/auth, roles: PUBLIC_ACCESS }
        - { path: ^/login, roles: PUBLIC_ACCESS }
        - { path: ^/api/prets, roles: IS_AUTHENTICATED_FULLY }
        - { path: ^/api, roles: ROLE_ADMIN }
```
### 5. Configurer LexikJWTAuthenticationBundle

Créez un fichier de configuration config/packages/lexik_jwt_authentication.yaml :

```yaml
lexik_jwt_authentication:
    secret_key: '%env(resolve:JWT_SECRET_KEY)%'
    public_key: '%env(resolve:JWT_PUBLIC_KEY)%'
    pass_phrase: '%env(JWT_PASSPHRASE)%'
    token_ttl: 3600
```

Ajoutez les variables d'environnement correspondantes dans le fichier .env :

```env
###> lexik/jwt-authentication-bundle ###
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_passphrase
###< lexik/jwt-authentication-bundle ###
```

### 6. Configurer API Platform

```yaml
api_platform:
    title: Hello API Platform
    version: 1.0.0
    formats:
        jsonld: ['application/ld+json']
    docs_formats:
        jsonld: ['application/ld+json']
        jsonopenapi: ['application/vnd.openapi+json']
        html: ['text/html']
    defaults:
        stateless: true
        cache_headers:
            vary: ['Content-Type', 'Authorization', 'Origin']
        extra_properties:
            standard_put: true
            rfc_7807_compliant_errors: true
    event_listeners_backward_compatibility_layer: false
    keep_legacy_inflector: false
    swagger:
        api_keys:
            JWT:
                name: Authorization
                type: header
```

### Partie JWT Refresh Token 

#### Pour une version de symfony 5.4+

#### Etape 1 : Installer le bundle 
```bash
composer require doctrine/orm doctrine/doctrine-bundle gesdinet/jwt-refresh-token-bundle
```

#### Etape 2: Authorizer le bundle 

```php
<?php

return [
    //...
    Gesdinet\JWTRefreshTokenBundle\GesdinetJWTRefreshTokenBundle::class => ['all' => true],
];
```
#### Etape 3: Configurer le bundle 

Configurez la classe de refresh token  
Créez le fichier config/packages/gesdinet_jwt_refresh_token.yaml avec le contenu ci-dessous :

```yaml
gesdinet_jwt_refresh_token:
    refresh_token_class: App\Entity\RefreshToken # This is the class name of the refresh token, you will need to adjust this to match the class your application will use
```

##### 2. Créé une classe dans src/Entity/RefreshToken.php

```bash
symfony console make:entity
# metter REFRESH TOKEN quand il vous demande le nom de la classe 
```

Une fois votre classe crée veuillez copié coller le contenu ci dessous 

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken as BaseRefreshToken;

/**
 * @ORM\Entity
 * @ORM\Table("refresh_tokens")
 */
#[ORM\Entity]
#[ORM\Table(name: 'refresh_tokens')]
class RefreshToken extends BaseRefreshToken
{
}
```

### Etape 4 : Définir la routes du refresh token 

Dans le fichier config/routes.yaml

```yaml
# config/routes.yaml
api_refresh_token:
    path: /api/token/refresh
# ...
```

#### Configurer le config/packages/security.yaml en ajoutant les ligens suivante :

```yaml
refresh_jwt:
                check_path: /api/token/refresh # or, you may use the `api_refresh_token` route name
                # or if you have more than one user provider
                # provider: user_provider_name
- { path: ^/api/(login|token/refresh), roles: PUBLIC_ACCESS }  
```       


### Conclusion

Vous avez maintenant configuré JWT Authentication avec API Platform dans votre projet Symfony. Cette configuration permet de sécuriser votre API en utilisant des tokens JWT, assurant ainsi que seuls les utilisateurs authentifiés peuvent accéder aux ressources protégées.