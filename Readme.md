# Проект Дианы Михайловой
==========================
# Установка
Запустить команду 

* apt install curl
* apt-get install make
* apt-get install docker-compose
* apt-get install git
* mkdir /var/www/ 
* git clone https://Xeror@bitbucket.org/diiianaaa/diana.git /var/www/m-diana.ru 

* Вводим пароль: aeyJeBnpmuKNyzNFZNnf 
* cd /var/www/m-diana.ru
* __make install__
* chmod -R 777 entrypoint.sh
* make up
* make vendor
* bash init-letsencrypt.sh
* rm docker/nginx/conf.d/default.conf.dist
* mv docker/nginx/conf.d/default.conf.ssl.dist docker/nginx/conf.d/default.conf.dist
* make up
* make doctrine-migrations-migrate

# Люблю *