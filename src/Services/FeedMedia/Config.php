<?php

/**
 * @file
 * Contains \Mpx\Services\FeedMedia.
 */

namespace Mpx\Services\FeedMedia;


use GuzzleHttp\Collection;

class Config extends Collection {

  /**
   * Creates Config from a config array.
   *
   * @param array $config
   *   Keyed array with the configuration parameters.
   *
   * @return static
   *   The Config instance.
   */
  public static function createFromConfig(array $config) {
    $defaults = array(
      'feed_type' => NULL,
      'feed' => FALSE,
      'ids' => array(),
      'owner_id' => NULL,
      'guids' => array(),
      'seo_terms' => array()
    );
    $required = array(
      'client',
      'account_pid',
      'feed_pid'
    );

    return static::fromConfig($config, $defaults, $required);
  }

}
