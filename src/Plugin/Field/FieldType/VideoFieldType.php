<?php

namespace Drupal\tt_video_field\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'tt_video_field_type' field type.
 *
 * @FieldType(
 *   id = "tt_video_field_type",
 *   label = @Translation("TT Video field"),
 *   description = @Translation("TT Video Field Type"),
 *   category = @Translation("Media"),
 *   default_widget = "tt_video_widget_type",
 *   default_formatter = "tt_video_formatter_type"
 * )
 */
class VideoFieldType extends FieldItemBase {

    /**
     * {@inheritdoc}
     */
    public static function defaultStorageSettings() {
        return [
            'max_length'     => 255,
            'is_ascii'       => FALSE,
            'case_sensitive' => FALSE,
          ] + parent::defaultStorageSettings();
    }

    /**
     * {@inheritdoc}
     */
    public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
        // Prevent early t() calls by using the TranslatableMarkup.
        $properties['url'] = DataDefinition::create('string')
          ->setLabel(new TranslatableMarkup('Video url'))
          ->setRequired(TRUE);

        $properties['title'] = DataDefinition::create('string')
          ->setLabel(new TranslatableMarkup('Title'))
          ->setRequired(TRUE);

        $properties['description'] = DataDefinition::create('string')
          ->setLabel(new TranslatableMarkup('Description'))
          ->setRequired(TRUE);

        return $properties;
    }

    /**
     * {@inheritdoc}
     */
    public static function schema(FieldStorageDefinitionInterface $field_definition) {
        $schema = [
          'columns' => [
            'url'         => [
              'type'   => 'varchar',
              'length' => (int)$field_definition->getSetting('max_length'),
            ],
            'title'       => [
              'type'   => 'varchar',
              'length' => (int)255,
            ],
            'description' => [
              'type'   => 'varchar',
              'length' => (int)255,
            ],

          ],
        ];

        return $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints() {
        $constraints = parent::getConstraints();

        if( $max_length = $this->getSetting('max_length') ) {
            $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
            $constraints[] = $constraint_manager->create('ComplexData', [
              'url' => [
                'Length' => [
                  'max'        => $max_length,
                  'maxMessage' => t('%name: may not be longer than @max characters.', [
                    '%name' => $this->getFieldDefinition()->getLabel(),
                    '@max'  => $max_length
                  ]),
                ],
              ],
            ]);
        }

        return $constraints;
    }

    /**
     * {@inheritdoc}
     */
    public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
        $random = new Random();
        $values['value'] = $random->word(mt_rand(1, $field_definition->getSetting('max_length')));

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
        $elements = [];

        $elements['max_length'] = [
          '#type'          => 'number',
          '#title'         => t('Maximum length'),
          '#default_value' => $this->getSetting('max_length'),
          '#required'      => TRUE,
          '#description'   => t('The maximum length of the field in characters.'),
          '#min'           => 1,
          '#disabled'      => $has_data,
        ];

        return $elements;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty() {
        $url = $this->get('url')->getValue();
        $title = $this->get('title')->getValue();
        $description = $this->get('description')->getValue();

        return empty($url) && empty($title) && empty($description);
    }

    /**
     * {@inheritdoc}
     */
    public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
        $form = [];

        $form['allowed_providers'] = [
          '#title'         => $this->t('Allowed Providers'),
          '#description'   => $this->t('Restrict users from entering information from the following providers. If none are selected any video provider can be used.'),
          '#type'          => 'checkboxes',
          '#default_value' => $this->getSetting('allowed_providers'),
          '#options'       => [
            'youtube' => $this->t('YouTube'),
            'vimeo'   => $this->t('Vimeo')
          ],
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public static function defaultFieldSettings() {
        return [
          'allowed_providers' => [],
        ];
    }

}
