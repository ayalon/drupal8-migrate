## Usage

- git clone the repo
- run "composer install"
- set up a virtual host pointing to the web folder in apache/nginx

## Set the path to the drupal configuration

- in settings.php adapt the path to the config folder
```
$config_directories['sync'] = 'sites/default/config_migrate
```

## Do a site install with drush

Drush 8 ist required.

```
drush site-install  config_installer \
--db-url=mysql://DBUSERNAME:DBPASSWORD@127.0.0.1/some_db \
--account-mail="admin@example.com" \
--account-name=admin \
--account-pass=some_admin_password \
--site-mail="admin@example.com" \
--site-name="Site-Install"
```

If you correctly set up the path in settings.php to the config folder, all settings will be imported during
the site install.

## Update configuration yml
Because of the CMI all yml files in the config/install directory are only imported when installing the module.
This is very impractical if one want to develop new configuration files.
To solve this, a module "Configuration Development" is part of this installation.
It is possible to import certain yml files on every request. But unfortunatly drush commands are not supported.
So we need to add this files we want to import to a new section in our module.info.yml. 

```yaml
config_devel:
  install:
    - migrate_plus.migration.page_node
    - migrate_plus.migration.menu_item
    - migrate_plus.migration_group.liip
```

Then we can run the following commands
after updating the yml file. This will import the new configuration file into CMI.

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

Then we import the comments referencing the  posts.
http://jsonplaceholder.typicode.com/comments