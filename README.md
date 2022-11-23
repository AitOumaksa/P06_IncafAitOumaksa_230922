### P06_IncafAitOumaksa_230922-Php/Symfony !
Création d'un site communautaire de partage de figures de snowboard via le framework Symfony.

### Environnement utilisé durant le développement :
Symfony 6.1.0

Composer 2.4.2

Bootstrap 5.2.0

- MampServer :

      Apache 2.4.33
      
      PHP 8.1.0
      
      MySQL  5.7.24

### Installation :

1-Clonez ou téléchargez le repository GitHub dans le dossier voulu :

    git clone https://github.com/AitOumaksa/P06_IncafAitOumaksa_230922.git 

2-Configurez vos variables d'environnement tel que la connexion à la base de données ou votre serveur SMTP ou adresse mail dans le fichier .env.local qui devra être crée à la racine du projet en réalisant une copie du fichier .env.

  
3-Téléchargez et installez les dépendances back-end du projet avec Composer :
  
      composer install
 
4-Au niveau de dossier /public/ Téléchargez et installez les dépendances front-end du projet avec Npm :

       npm install
       
5-Créez la base de données si elle n'existe pas déjà, taper la commande ci-dessous en vous plaçant dans le répertoire du projet :

      php bin/console doctrine:database:create
      
6-Créez les différentes tables de la base de données en appliquant les migrations :
 
      php bin/console doctrine:migrations:migrate 
      
 7-(Optionnel) Installer les fixtures pour avoir une démo de données fictives :
 
      php bin/console doctrine:fixtures:load
      
 Félications le projet est installé correctement, vous pouvez désormais commencer à l'utiliser à votre guise !
