<?php

/**
 * @file
 */


/**
 *
 */
function shkb_import_migrate_api() {
  $api = array(
    // Required - tells the Migrate module that you are implementing version 2
    // of the Migrate API.
    'api' => 2,
    // Migrations can be organized into groups. The key used here will be the
    // machine name of the group, which can be used in Drush:
    //  drush migrate-import --group=wine
    // The title is a required argument which is displayed for the group in the
    // UI. You may also have additional arguments for any other data which is
    // common to all migrations in the group.
    'groups' => array(
      'shkb' => array(
        'title' => t('shkb Import'),
      ),
    ),

    // Here we register the individual migrations. The keys (BeerTerm, BeerUser,
    // etc.) are the machine names of the migrations, and the class_name
    // argument is required. The group_name is optional (defaulting to 'default')
    // but specifying it is a best practice.
    'migrations' => array(
      'PagePlaceholder' => array(
        'class_name' => 'PagePlaceholderMigration',
        'group_name' => 'shkb',
      ),
      'MainMenu' => array(
        'class_name' => 'MainMenuMigration',
        'group_name' => 'shkb',
        'dependencies' => array(
          'PagePlaceholder',
        ),
      ),
    ),
  );
  return $api;
}
