<?php

namespace Drupal\tt_video_field\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'tt_video_formatter_type' formatter.
 *
 * @FieldFormatter(
 *   id = "tt_video_formatter_type",
 *   label = @Translation("Video formatter"),
 *   field_types = {
 *     "tt_video_field_type"
 *   }
 * )
 */
class VideoFormatterType extends FormatterBase {

    /**
     * {@inheritdoc}
     */
    public static function defaultSettings() {
        return [
              // Implement default settings.
          ] + parent::defaultSettings();
    }

    /**
     * {@inheritdoc}
     */
    public function settingsForm(array $form, FormStateInterface $form_state) {
        return [
              // Implement settings form.
          ] + parent::settingsForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function settingsSummary() {
        $summary = [];

        // Implement settings summary.

        return $summary;
    }

    /**
     * {@inheritdoc}
     */
    public function viewElements(FieldItemListInterface $items, $langcode) {
        $elements = [];

        foreach( $items as $delta => $item ) {
            $value = $this->viewValue($item);

            $elements[$delta] = [
              '#theme'       => 'tt_video_field',
              '#module_path' => drupal_get_path('module', 'tt_video_field'),
              '#instance_id' => $value['instance_id'],
              '#id'          => $value['id'],
              '#platform'    => $value['platform'],
              '#title'       => $value['title'],
              '#description' => $value['description'],
            ];
        }

        return $elements;
    }

    /**
     * Generate the output appropriate for one field item.
     *
     * @param \Drupal\Core\Field\FieldItemInterface $item
     *   One field item.
     *
     * @return string
     *   The textual output generated.
     */
    protected function viewValue(FieldItemInterface $item) {
        // The text value has no text format assigned to it, so the user input
        // should equal the output, including newlines.
        return [
          'instance_id' => 0,
          'id'          => $this->getVideoIdFromInput($item->url),
          'platform'    => $this->getPlatformFromInput($item->url),
          'title'       => Html::escape($item->title),
          'description' => nl2br(Html::escape($item->values['description'])),
        ];
    }

    private function getVideoIdFromInput($input) {
        preg_match('/^https?:\/\/(www\.)?((?!.*list=)youtube\.com\/watch\?.*v=|youtu\.be\/)(?<id>[0-9A-Za-z_-]*)/', $input, $matches);

        if( isset($matches['id']) ) {
            return $matches['id'];
        }

        preg_match('/^https?:\/\/(www\.)?vimeo.com\/(channels\/[a-zA-Z0-9]*\/)?(?<id>[0-9]*)(\/[a-zA-Z0-9]+)?(\#t=(\d+)s)?$/', $input, $matches);

        return isset($matches['id']) ? $matches['id'] : FALSE;
    }

    private function getPlatformFromInput($input) {
        preg_match('/^https?:\/\/(www\.)?((?!.*list=)youtube\.com\/watch\?.*v=|youtu\.be\/)(?<id>[0-9A-Za-z_-]*)/', $input, $matches);

        if( isset($matches['id']) ) {
            return 'youtube';
        }

        preg_match('/^https?:\/\/(www\.)?vimeo.com\/(channels\/[a-zA-Z0-9]*\/)?(?<id>[0-9]*)(\/[a-zA-Z0-9]+)?(\#t=(\d+)s)?$/', $input, $matches);

        return isset($matches['id']) ? 'vimeo' : FALSE;
    }

}
