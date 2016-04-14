<?php

/**
 * @file
 */

/**
 *
 */
abstract class BasicNodeMigration extends Migration {
  // A Migration constructor takes an array of arguments as its first parameter.
  /**
   * The arguments must be passed through to the parent constructor.
   */
  public function __construct($arguments) {
    parent::__construct($arguments);

    ini_set('auto_detect_line_endings', TRUE);

  }

}

/**
 *
 */
class PagePlaceholderMigration extends BasicNodeMigration {
  /**
   *
   */
  public function __construct($arguments) {
    parent::__construct($arguments);
    $this->description = t('Page Placeholder import');

    // Set up our destination - nodes of type migrate_example_beer.
    $this->destination = new MigrateDestinationNode('page');

    $this->csvFile = DRUPAL_ROOT . '/docs/navigation.csv';

    $this->map = new MigrateSQLMap($this->machineName,
      array(
        'ID' => array(
          'type' => 'int',
          'unsigned' => TRUE,
          'not null' => TRUE,
        ),
      ),
      MigrateDestinationNode::getKeySchema()
    );

    $this->source = new MigrateSourceCSV($this->csvFile, array(), array('header_rows' => 1, 'delimiter' => ';'));

    // Force update.
    $this->highwaterField = array();

    // Mapped fields.
    $this->addFieldMapping('title', 'name');
    $this->addFieldMapping('uid')->defaultValue(1);
    $this->addFieldMapping('status')->defaultValue(1);
    $this->addFieldMapping('promote')->defaultValue(0);
    $this->addFieldMapping('sticky')->defaultValue(0);
    $this->addFieldMapping('language')->defaultValue('de');

    // Unmapped destination fields.
    $this->addUnmigratedDestinations(array(
      'body:format',
      'changed',
      'comment',
      'created',
      'is_new',
      'log',
      'revision',
      'revision_uid',
      'tnid',
      'translate',
    ));

  }

  /**
   *
   */
  public function prepareRow($row) {

    // Magic: get the current page name.
    for ($i = 4; $i > 0; $i--) {
      if (!empty($row->{'Level_' . $i})) {
        $row->name = $row->{'Level_' . $i};
        break;
      }
    }
  }
}
