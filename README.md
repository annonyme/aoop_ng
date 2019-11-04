# aoop-ng

PHP Framework for hompages, blogs, middleware and microservices.

## CLI-Interface
```
php cli.php
```

## Web-Root
```
./public/
./public/index.php
```

## Install/Usage
- Clone the repository
- install requirements via composer **php utils/composer.phar install**
- Use Docker via Docker-Compose, if you want... ("docker-compose up -d" and open localhost:8081 in your browser)
- or go the old way with a local Apache/XAMPP (use CLI **aoop:core:install** to init the database or execute **etc/install/seperate/aoop_core.sql** on your own)
- login to the admin-panel via the Login-Form on the home-page or via URL with **index.php?username=admin&userpassword=admin&adminpage=1**

## Productive Usage
Change the default **admin**-password!

## Modules
- copy the module folder to the module-folder
- install via **aoop:modules:install**, if there is a SQL install-file