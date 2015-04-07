<?php
/**
 * @file
 * Contains \Mpx\Services\FeedMedia\ClientInterface
 */

namespace Mpx\Services\FeedMedia;

use Mpx\MpxException;
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
   * @return array
   *   An array of results.
   */
  public function fetch(array $options = array());

  /**
   * Returns the number of elements for the requested feed
   *
   * @param array $options
   *   The array of options as provided the the Guzzle client.
   *
   * @return int
   */
  public function count(array $options = array());

  /**
   * Builds the path to request based on the internal parameters.
   *
   * @throws MpxException
   *   For incompatible options.
   *
   * @return string
   *   The path.
   */
  public function buildPath();

}
