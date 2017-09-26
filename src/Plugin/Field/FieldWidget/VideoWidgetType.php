<?php

namespace Drupal\tt_video_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'tt_video_widget_type' widget.
 *
 * @FieldWidget(
 *   id = "tt_video_widget_type",
 *   label = @Translation("Video widget"),
 *   field_types = {
 *     "tt_video_field_type"
 *   }
 * )
 */
class VideoWidgetType extends WidgetBase {

    /**
     * {@inheritdoc}
     */
    public static function defaultSettings() {
        return [
            'size'        => 60,
            'placeholder' => '',
          ] + parent::defaultSettings();
    }

    /**
     * {@inheritdoc}
     */
    public function settingsForm(array $form, FormStateInterface $form_state) {
        $elements = [];

        $elements['size'] = [
          '#type'          => 'number',
          '#title'         => t('Size of textfield'),
          '#default_value' => $this->getSetting('size'),
          '#required'      => TRUE,
          '#min'           => 1,
        ];
        $elements['placeholder'] = [
          '#type'          => 'textfield',
          '#title'         => t('Placeholder'),
          '#default_value' => $this->getSetting('placeholder'),
          '#description'   => t('Text that will be shown inside the field until a value is entered. This hint is usually a sample value or a brief description of the expected format.'),
        ];

        return $elements;
    }

    /**
     * {@inheritdoc}
     */
    public function settingsSummary() {
        $summary = [];

        $summary[] = t('Textfield size: @size', ['@size' => $this->getSetting('size')]);
        if( !empty($this->getSetting('placeholder')) ) {
            $summary[] = t('Placeholder: @placeholder', ['@placeholder' => $this->getSetting('placeholder')]);
        }

        return $summary;
    }

    /**
     * {@inheritdoc}
     */
    public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
        $element['url'] = [
          '#type'              => 'url',
          '#title'             => $this->t('Video url'),
          '#default_value'     => isset($items[$delta]->url) ? $items[$delta]->url : NULL,
          '#size'              => $this->getSetting('size'),
          '#placeholder'       => $this->getSetting('placeholder'),
          '#maxlength'         => $this->getFieldSetting('max_length'),
          '#allowed_providers' => $this->getFieldSetting('allowed_providers'),

        ];

        $element['title'] = [
          '#type'          => 'textfield',
          '#title'         => $this->t('Title'),
          '#default_value' => isset($items[$delta]->title) ? $items[$delta]->title : NULL,
          '#size'          => 60,
          '#maxlength'     => 255,
        ];

        $element['description'] = [
          '#type'          => 'textfield',
          '#title'         => $this->t('Description'),
          '#default_value' => isset($items[$delta]->description) ? $items[$delta]->description : NULL,
          '#size'          => 60,
          '#maxlength'     => 255,

        ];


        $element += [
//          '#type' => 'fieldset',
          '#type' => 'details',
          '#open' => TRUE,
        ];


        return $element;
    }

}
