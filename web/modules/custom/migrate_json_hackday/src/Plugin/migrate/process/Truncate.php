<?php


namespace Drupal\migrate_json_hackday\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\Component\Utility\Unicode;

/**
 *
 * @MigrateProcessPlugin(
 *   id = "truncate"
 * )
 */
class Truncate extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    return Unicode::truncate($value, 64);

  }

}
