UserSample
==========

## step 1 : 

```
git clone https://github.com/deeppanara/UserSample.git
```

## step 2: build & up docker

```
docker-compose build
docker-compose up
```

## step 3: set up project  

```
docker exec -it app composer install
```

## step 4:   

```
chown $USER:$GROUP ./ -Rf
chmod -R 777 public/bundles public/uploads var/cache var/logs
```

## step 5: update database 

```
docker exec -it app bin/console doctrine:database:create
docker exec -it app bin/console doctrine:schema:update --force
docker exec -it app bin/console doctrine:fixtures:load
```

## URL:
 
User url: http://localhost:8000/

PhpMyA dmin url: http://localhost:8080/




