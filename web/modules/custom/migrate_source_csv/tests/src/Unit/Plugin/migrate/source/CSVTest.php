<?php
/**
 * @file
 * Code for CSVTest.php.
 */

namespace Drupal\Tests\migrate_source_csv\Unit\Plugin\migrate\source;

use Drupal\migrate\Plugin\Migration;
use Drupal\migrate_source_csv\Plugin\migrate\source\CSV;
use Drupal\Tests\migrate_source_csv\Unit\CSVUnitTestCase;
use Prophecy\Argument;

/**
 * @coversDefaultClass \Drupal\migrate_source_csv\Plugin\migrate\source\CSV
 *
 * @group migrate_source_csv
 */
class CSVTest extends CSVUnitTestCase {

  /**
   * The plugin id.
   *
   * @var string
   */
  protected $pluginId;

  /**
   * The plugin definition.
   *
   * @var array
   */
  protected $pluginDefinition;

  /**
   * The migration plugin.
   *
   * @topo Swap it out for a mock instance after https://www.drupal.org/node/2694009
   *
   * @var \Drupal\migrate\Plugin\Migration
   */
  protected $plugin;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->pluginId = 'test csv migration';
    $this->pluginDefinition = [];
    $plugin = $this->prophesize(Migration::class);
    $plugin->getIdMap()
      ->willReturn(NULL);
    // @topo Swap it out for getHighWaterProperty after https://www.drupal.org/node/2694009
    $plugin->get(Argument::exact('highWaterProperty'))
      ->willReturn(NULL);

    $this->plugin = $plugin->reveal();
  }

  /**
   * Tests the construction of CSV.
   *
   * @test
   *
   * @covers ::__construct
   */
  public function create() {
    $configuration = [
      'path' => $this->happyPath,
      'keys' => ['id'],
      'header_row_count' => 1,
    ];

    $csv = new CSV($configuration, $this->pluginId, $this->pluginDefinition, $this->plugin);

    $this->assertInstanceOf(CSV::class, $csv);
  }

  /**
   * Tests that a missing path will throw an exception.
   *
   * @test
   *
   * @expectedException \Drupal\migrate\MigrateException
   *
   * @expectedExceptionMessage You must declare the "path" to the source CSV file in your source settings.
   */
  public function migrateExceptionPathMissing() {
    new CSV([], $this->pluginId, $this->pluginDefinition, $this->plugin);
  }

  /**
   * Tests that missing keys will throw an exception.
   *
   * @test
   *
   * @expectedException \Drupal\migrate\MigrateException
   *
   * @expectedExceptionMessage You must declare "keys" as a unique array of fields in your source settings.
   */
  public function migrateExceptionKeysMissing() {
    $configuration = [
      'path' => $this->happyPath,
    ];

    new CSV($configuration, $this->pluginId, $this->pluginDefinition, $this->plugin);
  }

  /**
   * Tests that toString functions as expected.
   *
   * @test
   *
   * @covers ::__toString
   */
  public function toString() {
    $configuration = [
      'path' => $this->happyPath,
      'keys' => ['id'],
      'header_row_count' => 1,
    ];

    $csv = new CSV($configuration, $this->pluginId, $this->pluginDefinition, $this->plugin);

    $this->assertEquals($configuration['path'], (string) $csv);
  }

  /**
   * Tests initialization of the iterator.
   *
   * @test
   *
   * @covers ::initializeIterator
   */
  public function initializeIterator() {
    $configuration = [
      'path' => $this->happyPath,
      'keys' => ['id'],
      'header_row_count' => 1,
    ];

    $config_common = [
      'path' => $this->sad,
      'keys' => ['id'],
    ];
    $config_delimiter = ['delimiter' => '|'];
    $config_enclosure = ['enclosure' => '%'];
    $config_escape = ['escape' => '`'];

    $csv = new CSV($config_common + $config_delimiter, $this->pluginId, $this->pluginDefinition, $this->plugin);
    $this->assertEquals(current($config_delimiter), $csv->initializeIterator()
      ->getCsvControl()[0]);
    $this->assertEquals('"', $csv->initializeIterator()->getCsvControl()[1]);

    $csv = new CSV($config_common + $config_enclosure, $this->pluginId, $this->pluginDefinition, $this->plugin);
    $this->assertEquals(',', $csv->initializeIterator()->getCsvControl()[0]);
    $this->assertEquals(current($config_enclosure), $csv->initializeIterator()
      ->getCsvControl()[1]);

    $csv = new CSV($config_common + $config_delimiter + $config_enclosure + $config_escape, $this->pluginId, $this->pluginDefinition, $this->plugin);
    $csv_file_object = $csv->initializeIterator();
    $row = [
      '1',
      'Justin',
      'Dean',
      'jdean0@example.com',
      'Indonesia',
      '60.242.130.40',
    ];
    $csv_file_object->rewind();
    $current = $csv_file_object->current();
    $this->assertArrayEquals($row, $current);

    $csv = new CSV($configuration, $this->pluginId, $this->pluginDefinition, $this->plugin);
    $csv_file_object = $csv->initializeIterator();
    $row = [
      'id' => '1',
      'first_name' => 'Justin',
      'last_name' => 'Dean',
      'email' => 'jdean0@example.com',
      'country' => 'Indonesia',
      'ip_address' => '60.242.130.40',
    ];
    $second_row = [
      'id' => '2',
      'first_name' => 'Joan',
      'last_name' => 'Jordan',
      'email' => 'jjordan1@example.com',
      'country' => 'Thailand',
      'ip_address' => '137.230.209.171',
    ];

    $csv_file_object->rewind();
    $current = $csv_file_object->current();
    $this->assertArrayEquals($row, $current);
    $csv_file_object->next();
    $next = $csv_file_object->current();
    $this->assertArrayEquals($second_row, $next);

    $column_names = [
      'column_names' => [
        0 => ['id' => 'identifier'],
        2 => ['last_name' => 'User last name'],
      ],
    ];
    $csv = new CSV($configuration + $column_names, $this->pluginId, $this->pluginDefinition, $this->plugin);
    $csv_file_object = $csv->initializeIterator();
    $row = [
      'id' => '1',
      'last_name' => 'Dean',
    ];
    $second_row = [
      'id' => '2',
      'last_name' => 'Jordan',
    ];

    $csv_file_object->rewind();
    $current = $csv_file_object->current();
    $this->assertArrayEquals($row, $current);
    $csv_file_object->next();
    $next = $csv_file_object->current();
    $this->assertArrayEquals($second_row, $next);
  }

  /**
   * Tests that the key is properly identified.
   *
   * @test
   *
   * @covers ::getIds
   */
  public function getIds() {
    $configuration = [
      'path' => $this->happyPath,
      'keys' => ['id'],
      'header_row_count' => 1,
    ];

    $csv = new CSV($configuration, $this->pluginId, $this->pluginDefinition, $this->plugin);

    $expected = ['id' => ['type' => 'string']];
    $this->assertArrayEquals($expected, $csv->getIds());
  }

  /**
   * Tests that fields have a machine name and description.
   *
   * @test
   *
   * @covers ::fields
   */
  public function fields() {
    $configuration = [
      'path' => $this->happyPath,
      'keys' => ['id'],
      'header_row_count' => 1,
    ];
    $fields = [
      'id' => 'identifier',
      'first_name' => 'User first name',
    ];

    $expected = $fields + [
      'last_name' => 'last_name',
      'email' => 'email',
      'country' => 'country',
      'ip_address' => 'ip_address',
    ];

    $csv = new CSV($configuration, $this->pluginId, $this->pluginDefinition, $this->plugin);
    $csv = new CSV($configuration + ['fields' => $fields], $this->pluginId, $this->pluginDefinition, $this->plugin);
    $this->assertArrayEquals($expected, $csv->fields());

    $column_names = [
      0 => ['id' => 'identifier'],
      2 => ['first_name' => 'User first name'],
    ];
    $csv = new CSV($configuration + [
      'fields' => $fields,
      'column_names' => $column_names,
    ], $this->pluginId, $this->pluginDefinition, $this->plugin);
    $this->assertArrayEquals($fields, $csv->fields());
  }

}
