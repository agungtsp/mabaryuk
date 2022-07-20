# talent-hero

Requirements :
1. Database Minimum : MySQL 5.7
2. Web Hosting :
- PHP version 8.0
- Composer 2.0 or higher
- PDO PHP Extension (and relevant driver for the database you want to connect to)
- cURL PHP Extension
- OpenSSL PHP Extension
- Mbstring PHP Extension
- ZipArchive PHP Extension
- GD PHP Extension
- SimpleXML PHP Extension

How to install
1. run composer install
2. create db talent_hero
3. create & adjust .env
4. import db/backend_user.sql
5. run october:migrate
7. change config http://xxx/backend/system/settings/update/rainlab/user/settings#primarytab-activation > Activation mode = user
8. import db/mabaryuk_ref_locations.sql
9. import db/mabaryuk_master_status.sql
10. add lang id at https://xxx/backend/rainlab/translate/locales
11. import multilang.csv at https://xxx/backend/rainlab/translate/messages


(skip steps 4-9 if db already installed)

Backend User :
user : admin
pass : 123456

# Using docker
1. running command
```sh
docker-compose --env-file ./docker/config/.env.dev up -d
```

# Build Docker image
```sh
docker build -t mabaryuk-fpm:latest --target staging -f docker/php-fpm/Dockerfile .
```
