<?php
/**
 * @file
 * Contains \Mpx\Client
 */

namespace Mpx;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Message\Response;

class Client implements ClientInterface {

  /**
   * Guzzle client.
   *
   * @var GuzzleClient
   */
  protected $client;

  /**
   * Default options.
   *
   * @var array
   */
  public $defaults = array();

  /**
   * Constructs a Client object.
   *
   * @param \GuzzleHttp\Client $client
   *   The Guzzle client.
   * @param array $defaults
   *   The provided defaults.
   */
  public function __construct(GuzzleClient $client, array $defaults = array()) {
    $this->client = $client;
    $this->defaults = $defaults;
    $this->defaults += array(
      'form' => $this::FORMAT_RSS
    );
  }

  /**
   * {@inheritdoc}
   */
  public function get($path, array $options = array()) {
    return $this->client->get($path, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function parseBody(Response $response, $format = '') {
    if (empty($format)) {
      $format = isset($this->defaults['form']) ? $this->defaults['form'] : NULL;
    }
    if ($format == $this::FORMAT_JSON || $format == $this::FORMAT_CJSON) {
      return $response->json();
    }
    if ($format == $this::FORMAT_ATOM || $format == $this::FORMAT_RSS) {
      return $response->xml();
    }
    throw new MpxException('Custom formats are not supported.');
  }

}
