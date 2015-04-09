<?php
/**
 * @file
 * Contains \Mpx\Services\FeedMedia\ClientInterface
 */

namespace Mpx\Services\FeedMedia;

use Pimple\Container;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;

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
   * Gets the underlying guzzle client.
   *
   * Primarily used to modify the client for response mocking during testing.
   *
   * @return GuzzleClientInterface
   *   The client.
   */
  public function getGuzzleClient();

  /**
   * Set configuration key.
   *
   * @param string $key
   *   The key for the configuration element.
   * @param mixed $value
   *   The value for the configuration element.
   */
  public function setConfig($key, $value);

}
