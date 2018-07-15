<?php

namespace Drupal\Tests\color_field\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\Tests\BrowserTestBase;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Tests the creation of telephone fields.
 *
 * @group color_field
 */
class ColorFieldFormatterTest extends BrowserTestBase {

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
      'entity_type' => 'node',
      'bundle' => 'article',
    ])->save();

  }

  /**
   * Test color_field_formatter_text formatter.
   */
  public function testColorFieldFormatterText() {
    /** @var \Drupal\Core\Entity\Entity\EntityFormDisplay $form */
    $form = $this->entityTypeManager->getStorage('entity_form_display')
      ->load('node.article.default');
    $form->setComponent('field_color', [
      'type' => 'color_field_widget_default',
      'settings' => [
        'placeholder_color' => '#ABC123',
        'placeholder_opacity' => '1.0',
      ],
    ])->save();

    /** @var \Drupal\Core\Entity\Entity\EntityViewDisplay $display */
    $display = $this->entityTypeManager->getStorage('entity_view_display')
      ->load('node.article.default');
    $display->setComponent('field_color', [
      'type' => 'color_field_formatter_text',
      'weight' => 1,
    ])->save();

    // Display creation form.
    $this->drupalGet('node/add/article');
    $this->assertSession()->fieldExists("field_color[0][color]");
    $this->assertSession()->fieldExists("field_color[0][opacity]");
    $this->assertSession()->responseContains('placeholder="#ABC123"');
    $this->assertSession()->responseContains('placeholder="1.0"');
    $this->assertSession()->responseContains('Freeform Color');

    // Test basic entry of telephone field.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_color[0][color]' => "#E70000",
      'field_color[0][opacity]' => 1,
    ];

    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertSession()->responseContains('#E70000 1</div>');

    // Ensure alternate hex format works.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_color[0][color]' => "FF8C00",
      'field_color[0][opacity]' => 0.5,
    ];

    // Render without opacity value.
    $display->setComponent('field_color', [
      'type' => 'color_field_formatter_text',
      'weight' => 1,
      'settings' => [
        'opacity' => FALSE,
      ],
    ])->save();

    $this->drupalPostForm('node/add/article', $edit, t('Save'));
    $this->assertSession()->responseContains('#FF8C00</div>');

    // Test RGBA Render mode.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_color[0][color]' => "#FFEF00",
      'field_color[0][opacity]' => 0.9,
    ];
    $display->setComponent('field_color', [
      'type' => 'color_field_formatter_text',
      'weight' => 1,
      'settings' => [
        'format' => 'rgb',
        'opacity' => TRUE,
      ],
    ])->save();

    $this->drupalPostForm('node/add/article', $edit, t('Save'));
    $this->assertSession()->responseContains('RGBA(255,239,0,0.9)');

    // Test RGB render mode.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_color[0][color]' => "#00811F",
      'field_color[0][opacity]' => 0.8,
    ];
    $display->setComponent('field_color', [
      'type' => 'color_field_formatter_text',
      'weight' => 1,
      'settings' => [
        'format' => 'rgb',
        'opacity' => FALSE,
      ],
    ])->save();

    $this->drupalPostForm('node/add/article', $edit, t('Save'));
    $this->assertSession()->responseContains('RGB(0,129,31)');
  }

  /**
   * Test color_field_formatter_swatch formatter.
   */
  public function testColorFieldFormatterSwatch() {
    /** @var \Drupal\Core\Entity\Entity\EntityFormDisplay $form */
    $form = $this->entityTypeManager->getStorage('entity_form_display')
      ->load('node.article.default');
    $form->setComponent('field_color', [
      'type' => 'color_field_widget_default',
      'settings' => [
        'placeholder_color' => '#ABC123',
        'placeholder_opacity' => '1.0',
      ],
    ])->save();

    /** @var \Drupal\Core\Entity\Entity\EntityViewDisplay $display */
    $display = $this->entityTypeManager->getStorage('entity_view_display')
      ->load('node.article.default');
    $display->setComponent('field_color', [
      'type' => 'color_field_formatter_swatch',
      'weight' => 1,
    ])->save();

    // Test square with opacity.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_color[0][color]' => "#0044FF",
      'field_color[0][opacity]' => 0.9,
    ];

    $this->drupalPostForm('node/add/article', $edit, t('Save'));
    $this->assertSession()->responseContains('background-color: rgba(0,68,255,0.9)');
    $this->assertSession()->responseContains('color_field__swatch--square');

    // Test circle without opacity.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_color[0][color]' => "#760089",
      'field_color[0][opacity]' => 1,
    ];
    $display->setComponent('field_color', [
      'type' => 'color_field_formatter_swatch',
      'weight' => 1,
      'settings' => [
        'shape' => 'circle',
        'opacity' => FALSE,
      ],
    ])->save();

    $this->drupalPostForm('node/add/article', $edit, t('Save'));
    $this->assertSession()->responseContains('background-color: rgb(118,0,137)');
    $this->assertSession()->responseContains('color_field__swatch--circle');
  }

  /**
   * Test color_field_formatter_css formatter.
   */
  public function testColorFieldFormatterCss() {
    /** @var \Drupal\Core\Entity\Entity\EntityFormDisplay $form */
    $form = $this->entityTypeManager->getStorage('entity_form_display')
      ->load('node.article.default');
    $form->setComponent('field_color', [
      'type' => 'color_field_widget_default',
      'settings' => [
        'placeholder_color' => '#ABC123',
        'placeholder_opacity' => '1.0',
      ],
    ])->save();

    /** @var \Drupal\Core\Entity\Entity\EntityViewDisplay $display */
    $display = $this->entityTypeManager->getStorage('entity_view_display')
      ->load('node.article.default');
    $display->setComponent('field_color', [
      'type' => 'color_field_formatter_css',
      'weight' => 1,
    ])->save();

    // Test default options.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_color[0][color]' => "#FFF430",
      'field_color[0][opacity]' => 0.9,
    ];

    $this->drupalPostForm('node/add/article', $edit, t('Save'));
    $this->assertSession()->responseContains('body { background-color: RGBA(255,244,48,0.9) !important; }');

    // Test without opacity and not important.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_color[0][color]' => "#FFFFFF",
      'field_color[0][opacity]' => 1,
    ];
    $display->setComponent('field_color', [
      'type' => 'color_field_formatter_css',
      'weight' => 1,
      'settings' => [
        'selector' => 'body',
        'property' => 'background-color',
        'important' => FALSE,
        'opacity' => FALSE,
      ],
    ])->save();

    $this->drupalPostForm('node/add/article', $edit, t('Save'));
    $this->assertSession()->responseContains('body { background-color: RGB(255,244,48); }');

    // Test with token selector.
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_color[0][color]' => "#9C59D1",
      'field_color[0][opacity]' => 0.95,
    ];
    $display->setComponent('field_color', [
      'type' => 'color_field_formatter_css',
      'weight' => 1,
      'settings' => [
        'selector' => '.node-[node:content-type]',
        'property' => 'background-color',
        'important' => FALSE,
        'opacity' => TRUE,
      ],
    ])->save();

    $this->drupalPostForm('node/add/article', $edit, t('Save'));
    $this->assertSession()->responseContains('.node-article { background-color: RGBA(156,89,209,0.95); }');

    // Ensure 2 fields on the same entity are both rendered properly.
    FieldStorageConfig::create([
      'field_name' => 'field_text_color',
      'entity_type' => 'node',
      'type' => 'color_field_type',
    ])->save();
    FieldConfig::create([
      'field_name' => 'field_text_color',
      'label' => 'Text Color',
      'entity_type' => 'node',
      'bundle' => 'article',
    ])->save();

    $display->setComponent('field_text_color', [
      'type' => 'color_field_formatter_css',
      'weight' => 1,
      'settings' => [
        'selector' => '.node-[node:content-type]',
        'property' => 'color',
        'important' => FALSE,
        'opacity' => TRUE,
      ],
    ])->save();
    $edit = [
      'title[0][value]' => $this->randomMachineName(),
      'field_color[0][color]' => "#000000",
      'field_color[0][opacity]' => 0.1,
      'field_text_color[0][color]' => "#000000",
      'field_text_color[0][opacity]' => 1,
    ];
    $this->drupalPostForm('node/add/article', $edit, t('Save'));
    $this->assertSession()->responseContains('.node-article { background-color: RGBA(0,0,0,0.1); }');
    $this->assertSession()->responseContains('.node-article { color: RGBA(0,0,0,1); }');
  }

}
