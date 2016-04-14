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
 * Source plugin for beer content.
 *
 * @MigrateSource(
 *   id = "page_node"
 * )
 */
class PageNode extends CSV {

  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    //override the path to point to the local module directory
    $this->configuration['path'] = drupal_get_path('module', 'migrate_menu') . '/data/navigation_small.csv';

  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    // Magic: get the current page name.
    for ($i = 4; $i > 0; $i--) {
      if (!empty($row->{'Level_' . $i})) {
        $row->name = $row->{'Level_' . $i};
        break;
      }
    }

    return parent::prepareRow($row);
  }

}
