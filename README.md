```

git clone git@github.com:dralexsand/parserxls.git

cd wrapper

cp .env.dist .env

docker-compose up --build -d

docker exec -it parserxls_backend bash

composer install

chmod u+x init.sh
./init.sh (yes)

php yii parser DevTest2022MASTERBUDGET.xlsx

Сайт (result json):
http://parserxls.local:8089

Adminer:
http://parserxml.local:8989

```