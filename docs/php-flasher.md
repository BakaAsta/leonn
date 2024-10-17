![alt text](image-1.png)

### Installation pour Symfony
```bash
composer require php-flasher/flasher-symfony
```

Mettre en place les assets pour phpFalsher
```bash
php bin/console flasher:install
```

### Utilisation classique 

```php
#/ usage success
flash()->success('Your account has been re-verified.');
#/ usage error
flash()->error('There was a problem re-activating your account.');
#/ usage warning
flash()->warning('Your account may not have been de-registered.');
#/ usage info
flash()->info('Your account has been unlocked and a confirmation email has been sent.');
```

### Usage complexe

!!! info
    Vous pouvez passez des option a php-flasher
    ```php
    flash()
    ->option('position', 'bottom-right')
    ->option('timeout', 3000)
    ->error('There was an issue submitting your feedback.');
    ```

Pour plus de détail, veuillez vous référez à la documentation officielle

###  LIBRARIES PHPFlasher
!!! info
    Il faut savoir que intègre des librairies qui vous permettent d'ajuster le style de vos Message Flash. 
    Actuellement nous utilisons une de ces librairie qui s'appelle notyf, il possède 3 autres, je vous invite à cliquer sur les lien pour en savoir d'avantage :
    
    - [Noty](https://php-flasher.io/library/noty/)
    - [Sweetalert](https://php-flasher.io/library/sweetalert/)
    - [Toastr](https://php-flasher.io/library/toastr/)

### Installation de notyf pour Symfony 

```bash
composer require php-flasher/flasher-notyf-symfony
```
Mettre en place les assets pour phpFalsher
```bash
php bin/console flasher:install
```

!!! info "Configuration fichier [config/packages/flasher.yaml](/config/packages/flasher.yaml)"
    Vous trouverez une ligne `default` qui par défaut prend `phpFlasher`.
    Pour modifier cette configuration, il faut mettre `notyf` à la place.
    
    ```yaml
    # default: votre ancienne config
    default: notyf
    ```








     


 