<?php
/**
 * @file
 * Contains \Mpx\Services\FeedMedia\ClientInterface
 */

namespace Mpx\Services\FeedMedia;

use Pimple\Container;

interface ClientInterface {

  /**
   * Creates a Client object.
   *
   * @param Container $container
   *  A Pimple container with the Client object parameters.
   *
   * @return Client
   *   A Client object.
   */
  public static function create(Container $container);

  /**
   * Gets the contents of the feed.
   *
   * @param array $options
   *   The array of options as provided the the Guzzle client.
   *
   * @return Object|Bag
   *   An array of results.
   */
  public function get(array $options = array());

  /**
   * Returns the number of elements for the requested feed
   *
   * @return int
   */
  public function count();

}
