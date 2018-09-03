<?php

namespace Drupal\color_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the color_field box widget.
 *
 * @FieldWidget(
 *   id = "color_field_widget_box",
 *   module = "color_field",
 *   label = @Translation("Color boxes"),
 *   field_types = {
 *     "color_field_type"
 *   }
 * )
 */
class ColorFieldWidgetBox extends ColorFieldWidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'default_colors' => '
#AC725E,#D06B64,#F83A22,#FA573C,#FF7537,#FFAD46
#42D692,#16A765,#7BD148,#B3DC6C,#FBE983
#92E1C0,#9FE1E7,#9FC6E7,#4986E7,#9A9CFF
#B99AFF,#C2C2C2,#CABDBF,#CCA6AC,#F691B2
#CD74E6,#A47AE2',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['default_colors'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Default colors'),
      '#default_value' => $this->getSetting('default_colors'),
      '#required' => TRUE,
      '#description' => $this->t('Default colors for pre-selected color boxes'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $default_colors = $this->getSetting('default_colors');

    if (!empty($default_colors)) {
      preg_match_all("/#[0-9a-fA-F]{6}/", $default_colors, $default_colors, PREG_SET_ORDER);
      foreach ($default_colors as $color) {
        $colors = $color[0];
        $summary[] = $colors;
      }
    }

    if (empty($summary)) {
      $summary[] = $this->t('No default colors');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['#attached']['library'][] = 'color_field/color-field-widget-box';

    // Set Drupal settings.
    $settings[$element['#uid']] = [
      'required' => $this->fieldDefinition->isRequired(),
    ];
    $default_colors = $this->getSetting('default_colors');
    preg_match_all("/#[0-9a-fA-F]{6}/", $default_colors, $default_colors, PREG_SET_ORDER);
    foreach ($default_colors as $color) {
      $settings[$element['#uid']]['palette'][] = $color[0];
    }
    $element['#attached']['drupalSettings']['color_field']['color_field_widget_box']['settings'] = $settings;

    $element['color']['#suffix'] = "<div class='color-field-widget-box-form' id='" . $element['#uid'] . "'></div>";

    return $element;
  }

}
