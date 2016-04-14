## Usage

- git clone the repo
- run "composer install"
- set up a virtual host pointing to the web folder
- run drupal install with standard profile:

manually or with

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
- update migration yml

drush cdi1 migrate_menu/config/install/migrate_plus.migration.page_node.yml