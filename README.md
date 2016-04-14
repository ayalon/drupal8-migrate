## Usage

- git clone the repo
- run "composer install"
- set up a virtual host pointing to the web folder
- run drupal install with standard profile manually 

or with the following command

```
drush site-install  standard \
--db-url=mysql://DBUSERNAME:DBPASSWORD@localhost/some_db \
--account-mail="admin@example.com" \
--account-name=admin \
--account-pass=some_admin_password \
--site-mail="admin@example.com" \
--site-name="Site-Install"
```

- import configuration using "drush config-import"

## Update configuration yml
```
drush cdi migrate_menu
drush cr
```

## Migration tasks
drush migrate-status (ms)
drush migrate-import (mi)

```
drush mi page_node
drush mi menu_item --update
```

## Task for the Hackday:

Let's write an importer for a json source, usually a rest interface.

I propose we use this JSON data:
http://jsonplaceholder.typicode.com/posts

Import the posts into a node type "post"