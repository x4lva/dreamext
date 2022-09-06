TO RUN PROJECT USE FOLOWING COMMANDS

```
git clone https://github.com/x4lva/dreamext.git
cd dreamext

docker-compose build
docker-compose up -d
docker-compose php-fpm composer i
docker-compose run php-fpm php bin/console d:d:d --force
docker-compose run php-fpm php bin/console d:d:c
docker-compose run php-fpm php bin/console d:s:u --force
docker-compose run php-fpm php bin/console doctrine:fixtures:load

docker-compose run node yarn install
docker-compose run node yarn run dev
```

http://localhost:8080/ - Running website

http://localhost:8081/ - Mailhog mailer

http://localhost:8082/ - Adminer [Server: mysql, User: root, Password: dreamext, Database: dreamext]

http://localhost:8080/login 

AS User:  ```user@gmail.com password```

AS Admin: ```admin@gmail.com password```

