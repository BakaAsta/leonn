```twig
<twig:Tableau entityType="{{ entityType }}" titre="{{ titre }}" titreBouton="{{ titreBouton }}" actionButton="{{ actionButton }}" listeTh="{{ liste_th }}" listeAttributs="{{ listeAttributs }}" listeObjets="{{ listeObjets }}" urlModif="{{ urlModif }}" urlSupp="{{ urlSupp }}"/>
```

Les paramètre du composant précédé d'une <span style="color:red;">"*"</span> sont obligatoire 

!!! info Paramètres

    <span style="color:red;">*</span>
    entityType = type de l'entité 
    Valeurs possibles :

    - `pret`

    - `categorie`

    - `produit`

    - `carte`

    - `marque`

    - `typeProduit`

    - `user`

    <span style="color:red;">*</span>
    titre = titre du tableau

    titreBouton = titre du bouton d'action 
    
    !!! info 
        <span style="color:red;">*</span>
        actionButton = l'action si le bouton doit l'etre 
        lui passer la route de cette manière  

        !!! exemple 
            app_nom_de_ma_route
    
    <span style="color:red;">*</span>listeTh = liste des th de votre tableau

    <span style="color:red;">*</span>listeAttributs = liste des attributs de votre tableau

    <span style="color:red;">*</span>listeObjets = liste des objetc à afficher (produit, carte, etc...)

    !!! info
        urlModif = l'url qui s'écrit pareil que pour l'action du bouton mais pour la modification d'un élement
        !!! Exemple_pour_vos_cartes    
            app_carte_editer

    !!! info
        urlSupp = l'url qui s'écrit pareil que pour l'action du bouton mais pour la suppression d'un élement
        !!! Exemple_pour_vos_cartes    
            app_carte_delete





!!! warning Attention
    un snippet est disponible dans le projet 
    ```twig
    Tableau
    ```
