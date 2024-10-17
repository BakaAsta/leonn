!!! warning Respectez l'ordre du readme 

!!! info Pensez à importer les dépendances php 
    ```bash 
    composer install
    ```
    !!! danger Composer pas installer
        Veuillez vous référez au scrip de [configuration](/readmeRessources/setup.sh)

!!! info Pensez à importer les dépendances js 
    ```bash 
    yarn install
    ```
    !!! danger Yarn pas installer
        Veuillez vous référez au scrip de [configuration](/readmeRessources/setup.sh)

### Pensez à mettre en place typesense

##### Partie Docker 

!!! info 
    Bien respectez l'indentation du code yaml est très capicieux 

#### Etape 1 : Configuration de votre yaml
```yaml
version : "3.7"
services:
    typesense_docker_symfony:
        # version ici pour moi c 26.0, cela peut évoluer à adapter 
        image: typesense/typesense:26.0 
        # nom de votre container, il est conseillé de mettre le meme nom que celui du service
        container_name: typesense_docker_symfony
        #  redémarre en cas d'échec
        restart: on-failure
        # on expose sur un port libre le service
        ports:
        - "8108:8108"
        # on veut que les données soit sauvegarder, on les lient à ds volumes docker
        volumes:
        - typesense-data:/data
        command: '--data-dir /data --api-key=xyz --enable-cors'
        # Si vous posséder un réseau il faut écrire networks
        # puis le nom de votre réseau ainsi que ces paramètre 
        networks:
        dev:
            ipv4_address: 172.21.0.7
networks:
  dev:
    ipam:
      config:
        - subnet: 172.21.0.0/24
volumes:
  typesense-data:
```
!!! info Aide
    Si vous rencontrez d'autre problème veuillez vous référé au script de config ainsi qu'au fichier suivant

    - [docker-compose.yaml](/readmeRessources/docker-compose.yaml)
    - [setup.sh](/readmeRessources/setup.sh)

#### Etape 2 : Lancez votez docker

```bash 
docker-compose up -d 
```

### Etape 2.5 

!!! info 
    Si jamais la base de donées a subit des changements, ajout, modif, suppression sur les entités. Il vous faut mettre a jour le shéma de base de données 

    ```bash
    # d:m:m pour doctrine:migrations:migrate
    # Il va jouer toute les migration de différence entre votre shéma actuel et celui décrit par les migrations
    symfony console d:m:m
    ```
    
#### Etape 3 : Crée la base de données typesense

```bash
# t:c pour typesense:create
symfony console t:c
```
#### Etape 4 : Importer les données dans la base de donées typesense

```bash
# t:i pour typesense:import
symfony console t:i
```
### Mettre en place JWT 

Je vous laisse consultez notre [documentation](/docs/Api-platform.md)

### Message flash

Désormais notre application utilise une bibliothèque nommé php-falsher pour ces fameux message flash

Consultez la [documentaion](/docs/php-flasher.md)  pour l'implémenter et savoir comment l'utiliser

!!! 
    Après avoir suivis toute ces étape votre site devrai être fonctionnel 
    Bon developpement :)

### Lancez votre site

```bash 
symfony serve -d
```
Puis 

```bash 
yarn watch
```


!!! info Si vous souhaitez écrire des documentation    
    # mkdocs

    ```bash
    # On crée un dossier qui s'appelle my-project que vous pouvez changer à votre guise
    pip install mkdocs
    pip install mkdocs-glightbox
    ```

    Pour lancer le serveur :
    ```bash
    mkdocs serve -a localhost:8080
    ```

    Ajouter `-a` permet de changer le port par défaut. Utilisez-le si le serveur local de Symfony utilise déjà localhost:8080. Cela sert à forcer le serveur mkdocs une url et un port imposé  `localhost:8080`

    # 
    //TODO Ajouter la liste des commande pour initialiser le projet

