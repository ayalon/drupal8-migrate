<?php

/**
 * @file
 * Contains \Drupal\migrate_example\Plugin\migrate\source\BeerNode.
 */

namespace Drupal\migrate_menu\Plugin\migrate\source;

use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;

/**
 * Source plugin for page node.
 *
 * @MigrateSource(
 *   id = "page_node"
 * )
 */
class PageNode extends CSV {

  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    //override the path to point to the local module directory
    $this->configuration['path'] = drupal_get_path('module', 'migrate_menu') . '/data/navigation.csv';
    $this->configuration['delimiter'] = ';';

  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    // Magic: get the current page name.
    $csv_data = $row->getSource();
    for ($i = 4; $i > 0; $i--) {
      if (!empty($csv_data['Level_' . $i])) {
        $row->setSourceProperty('name', $csv_data['Level_' . $i]);
        break;
      }
    }

    return parent::prepareRow($row);
  }

}
