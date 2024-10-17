# parti installation php 8.2 
# si vous souhaitez installer une autre version de php, vous pouvez le faire en modifiant le numéro de version dans la commande qui seras précisé d'un #
sudo apt update && sudo apt upgrade 
sudo apt install software-properties-common ca-certificates lsb-release apt-transport-https 
LC_ALL=C.UTF-8 sudo add-apt-repository ppa:ondrej/php 
sudo apt update
sudo apt install php8.2 

# quelque option de base pour php
sudo apt install php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-intl

# on met la version de php 8.2 par défaut
sudo update-alternatives --set php /usr/bin/php8.2

# Modification version 
# sudo update-alternatives --set php /usr/bin/version_php

# Installation de nodejs 

sudo apt install curl 

# pour supprimer curl 
# sudo apt remove curl

# acutellement nous sommes à la version 22.x qui est stable et fonctionnel peut etre ile faudra mettre a jour cette partie la
# le fait de mettre par default la dernière version peut causer des problèmes, a vous d'ajuster

curl -sL https://deb.nodesource.com/setup_22.x | sudo -E bash - 

sudo apt-get install -y nodejs

# pour mettre a jour npm
npm install -g npm@latest


# Supprimer nodejs
# sudo apt-get remove nodejs
# ou 
# sudo apt purge nodejs  

# Installation de yarn 

sudo apt update

curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list
sudo apt update
sudo apt install yarn --no-install-recommends

# desinstallation de yarn

# npm uninstall -g yarn

# Installation de composer

sudo apt update && sudo apt upgrade 

sudo apt install php-cli php-zip unzip curl 

sudo curl -sS https://getcomposer.org/installer -o composer-setup.php 

sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer 

composer --version 

# desinstallation de composer

# sudo rm -rf /usr/local/bin/composer

# Installation de symfonyCli

wget https://get.symfony.com/cli/installer -O - | bash

mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Installer portainer 

sudo apt update

sudo apt install docker.io -y

# regardez si le server tourne 
sudo systemctl status docker

sudo systemctl start docker

sudo usermod -aG docker $USER

sudo docker pull portainer/portainer-ce:latest

# regardez si portainer est bien installé
docker images

docker run -d -p 9000:9000 --restart always -v /var/run/docker.sock:/var/run/docker.sock portainer/portainer-ce:latest

# regarder si le services tourne
docker ps

sudo apt  install docker-compose

docker-compose up -d