<?php

/**
 * @file
 */

/**
 *
 */
class MainMenuMigration extends BasicNodeMigration {

  public function __construct($arguments) {
    parent::__construct($arguments);
    $this->description = t('Page Placeholder import');

    // https://www.drupal.org/node/1117454
    // $this->systemOfRecord = Migration::DESTINATION;
    // The csv file contains the header.
    $this->csvFile = DRUPAL_ROOT . '/docs/navigation.csv';
    $this->source = new MigrateSourceCSV($this->csvFile,
      array(),
      array('header_rows' => 1, 'delimiter' => ';'));
    $this->destination = new MigrateDestinationMenuLinks();
    $this->map = new MigrateSQLMap($this->machineName,
      array(
        'ID' => array(
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
          'description' => 'ID of destination link',
        ),
      ),
      MigrateDestinationMenuLinks::getKeySchema()
    );

    // Always Main menu.
    $this->addFieldMapping('menu_name')->defaultValue('main-menu');
    $this->addFieldMapping('plid', 'Parent_ID')
      ->sourceMigration($this->getMachineName());
    $this->addFieldMapping('link_path', 'ID')
      ->callbacks(array($this, 'generatePath'))
      ->sourceMigration('PagePlaceholder');

    $this->addFieldMapping('router_path')->defaultValue('node/%');
    // Title of the menu item.
    $this->addFieldMapping('link_title', 'name');
    // Internal.
    $this->addFieldMapping('external')->defaultValue('0');
    $this->addFieldMapping('expanded')->defaultValue('1');
    // Weight.
    $this->addFieldMapping('weight', 'ID');
    // Not customized.
    $this->addFieldMapping('customized')->defaultValue('1');
    // $this->addFieldMapping('has_children')->defaultValue('0');  // Will be overridden automatically
    // $this->addFieldMapping('depth')->defaultValue('1'); // Will be overridden automatically.
    $this->addUnmigratedDestinations(array(
      'module',
      'hidden',
      'options',
      'p1',
      'p2',
      'p3',
      'p4',
      'p5',
      'p6',
      'p7',
      'p8',
      'p9',
      'updated',
    ));
  }

  /**
   *
   */
  protected function generatePath($value) {
    return 'node/' . $value;
  }


  /**
   *
   */
  public function prepareRow($row) {
    if (parent::prepareRow($row) === FALSE) {
      return FALSE;
    }

    $row->name = '';
    $row->parent = '';

    // Super Macic function for defining parent term
    // This magic allows us to avoid relying on the name while finding the parent term.
    for ($i = 4; $i > 0; $i--) {
      if (!empty($row->{'Level_' . $i})) {
        $row->name = $row->{'Level_' . $i};
        if ($i > 1) {
          $row->parent = $row->{'Level_' . ($i - 1)};
        }

        break;
      }
    }

  }


  /**
   * <a href="http://drupal.org/node/1403044#comment-5790748
   * " title="http://drupal.org/node/1403044#comment-5790748
   * ">http://drupal.org/node/1403044#comment-5790748
   * </a>   * Creates a stub menu link, for when a child is imported before its parent.
   *
   * @param $migration
   *   The source migration
   *
   * @return
   *  int $mlid on success
   *  FALSE on failure
   */
  protected function createStub($migration) {
    // If ref_parent is 0, that means it has no parent, so don't create a stub.
    if (!$migration->sourceValues->parent) {
      return FALSE;
    }
    $menu_link = array(
      'menu_name' => 'main-menu',
      'link_path' => 'stub-path',
      'router_path' => 'stub-path',
      'link_title' => t('Stub title'),
    );
    $mlid = menu_link_save($menu_link);
    if ($mlid) {
      return array($mlid);
    }
    else {
      return FALSE;
    }
  }
}
