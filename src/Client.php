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
    $output = NULL;
    if (empty($format)) {
      $format = isset($this->defaults['form']) ? $this->defaults['form'] : NULL;
    }
    if ($format == $this::FORMAT_JSON || $format == $this::FORMAT_CJSON) {
      $output = $response->json();
    }
    if ($format == $this::FORMAT_ATOM || $format == $this::FORMAT_RSS) {
      $output = $this->xmlToArray($response->xml());
    }
    if ($output['isException']) {
      throw new MpxException(sprintf('Exception returned: %s', print_r($output, TRUE)), $output['responseCode']);
    }
    if (isset($output)) {
      return $output;
    }
    throw new MpxException('Custom formats are not supported.');
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaults() {
    return $this->defaults;
  }

  /**
   * {@inheritdoc}
   */
  public function getGuzzleClient() {
    return $this->client;
  }

  /**
   * Converts a SimpleXMLElement to an array.
   *
   * @param \SimpleXMLElement $xml_object
   *   The object to convert.
   * @param array $out
   *   Temporary array, used for recursion.
   *
   * @return array
   *   The transformed array.
   */
  protected function xmlToArray(\SimpleXMLElement $xml_object, array $out = array ()) {
    foreach ((array) $xml_object as $index => $node ) {
      $out[$index] = (is_object($node)) ? $this->xmlToArray($node) : $node;
    }

    return $out;
  }

}
