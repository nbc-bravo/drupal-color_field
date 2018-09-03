<?php

namespace Drupal\Tests\color_field\FunctionalJavascript;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\FunctionalJavascriptTests\JavascriptTestBase;

/**
 * Tests for form grouping elements.
 *
 * @group form
 */
class ColorFieldWidgetJavascriptTests extends JavascriptTestBase {

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
   * The Entity View Display for the article node type.
   *
   * @var \Drupal\Core\Entity\Entity\EntityViewDisplay
   */
  protected $display;

  /**
   * The Entity Form Display for the article node type.
   *
   * @var \Drupal\Core\Entity\Entity\EntityFormDisplay
   */
  protected $form;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->drupalCreateContentType(['type' => 'article']);
    $user = $this->drupalCreateUser(['create article content', 'edit own article content']);
    $this->drupalLogin($user);
    $entityTypeManager = $this->container->get('entity_type.manager');
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
      'required' => TRUE,
    ])->save();
    FieldStorageConfig::create([
      'field_name' => 'field_color_repeat',
      'entity_type' => 'node',
      'type' => 'color_field_type',
    ])->save();
    FieldConfig::create([
      'field_name' => 'field_color_repeat',
      'label' => 'Repeat Color',
      'description' => 'Color repeat description',
      'entity_type' => 'node',
      'bundle' => 'article',
      'required' => FALSE,
    ])->save();
    $this->form = $entityTypeManager->getStorage('entity_form_display')
      ->load('node.article.default');
    $this->display = $entityTypeManager->getStorage('entity_view_display')
      ->load('node.article.default');
  }

  /**
   * Test color_field_widget_box.
   */
  public function testColorFieldWidgetBox() {
    $this->form
      ->setComponent('field_color_repeat', [
        'type' => 'color_field_widget_box',
        'settings' => [
          'default_colors' => '#FF0000,#FFFFFF',
        ],
      ])
      ->setComponent('field_color', [
        'type' => 'color_field_widget_box',
        'settings' => [
          'default_colors' => '#007749,#000000,#FFFFFF,#FFB81C,#E03C31,#001489',
        ],
      ])
      ->save();

    $session = $this->getSession();
    $web_assert = $this->assertSession();

    // Request the group details testing page.
    $this->drupalGet('node/add/article');

    $page = $session->getPage();

    // Wait for elements to be generated.
    $web_assert->waitForElementVisible('css', '#color-box-field-color_repeat button');

    $boxes = $page->findAll('css', '#color-box-field-color-repeat button');
    $this->assertEquals(3, count($boxes));

    // Confirm that two fields aren't sharing settings.
    $boxes = $page->findAll('css', '#color-box-field-color button');
    $this->assertEquals(6, count($boxes));

    /** @var \Behat\Mink\Element\NodeElement $box */
    $box = $boxes[0];
    $this->assertEquals('#007749', $box->getAttribute('color'));

    // Confirm that clicking the swatch sets the field value.
    $box->click();
    $field = $page->findField('field_color[0][color]');
    $this->assertEquals('#007749', $field->getValue());

  }


  /**
   * Test color_field_widget_spectrum widget.
   *
   * Unfortunately since the spectrum library uses clickable divs instead of
   * buttons, we can't use full interaction of clicks with elements. So instead
   * we just confirm that the right html has been generated and assume that the
   * library tests itself.
   */
  public function testColorFieldSpectrum() {
    $this->form
      ->setComponent('field_color_repeat', [
        'type' => 'color_field_widget_spectrum',
        'settings' => [
          'palette' => '["#0678BE","#53B0EB", "#96BC44"]',
          'show_palette' => FALSE,
        ],
      ])
      ->setComponent('field_color', [
        'type' => 'color_field_widget_spectrum',
        'settings' => [
          'palette' => '["#005493","#F5AA1C","#C63527","002754"]',
          'show_palette' => TRUE,
        ],
      ])
      ->save();

    $session = $this->getSession();
    $web_assert = $this->assertSession();

    // Request the group details testing page.
    $this->drupalGet('node/add/article');

    $page = $session->getPage();

    // Wait for elements to be generated.
    $web_assert->waitForElementVisible('css', '.sp-preview');

    // Confirm that two fields aren't sharing settings.
    // Also confirms that custom palette is being used correctly
    // and that the one field's palette isn't shown.
    $boxes = $page->findAll('css', '.sp-thumb-el');
    $this->assertEquals(4, count($boxes));


  }

}
