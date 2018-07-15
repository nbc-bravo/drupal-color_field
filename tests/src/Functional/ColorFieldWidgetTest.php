<?php

namespace Drupal\Tests\color_field\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\Tests\BrowserTestBase;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Tests color field widgets.
 *
 * @group color_field
 */
class ColorFieldWidgetTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = [
    'field',
    'node',
    'color_field',
  ];

  /**
   * The Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * A user with permission to create articles.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupalCreateContentType(['type' => 'article']);
    $this->webUser = $this->drupalCreateUser(['create article content', 'edit own article content']);
    $this->drupalLogin($this->webUser);
    $this->entityTypeManager = $this->container->get('entity_type.manager');
    FieldStorageConfig::create([
      'field_name' => 'field_color',
      'entity_type' => 'node',
      'type' => 'color_field_type',
    ])->save();
    FieldConfig::create([
      'field_name' => 'field_color',
      'label' => 'Freeform Color',
      'description' => 'Color field description',
      'entity_type' => 'node',
      'bundle' => 'article',
    ])->save();

  }

  /**
   * Test color_field_widget_html5.
   */
  public function testColorFieldWidgetHtml5() {
    /** @var \Drupal\Core\Entity\Entity\EntityFormDisplay $form */
    $form = $this->entityTypeManager->getStorage('entity_form_display')
      ->load('node.article.default');
    $form->setComponent('field_color', [
      'type' => 'color_field_widget_html5',
    ])->save();

    /** @var \Drupal\Core\Entity\Entity\EntityViewDisplay $display */
    $display = $this->entityTypeManager->getStorage('entity_view_display')
      ->load('node.article.default');
    $display->setComponent('field_color', [
      'type' => 'color_field_formatter_text',
      'weight' => 1,
    ])->save();

    // Confirm field label and description are rendered.
    $this->drupalGet('node/add/article');
    $this->assertSession()->fieldExists("field_color[0][color]");
    $this->assertSession()->fieldExists("field_color[0][opacity]");
    $this->assertSession()->responseContains('Freeform Color');
    $this->assertSession()->responseContains('Color field description');

    // Test basic entry of color field.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_color[0][color]' => "#E70000",
      'field_color[0][opacity]' => 1,
    ];

    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertSession()->responseContains('#E70000 1</div>');
  }

}
