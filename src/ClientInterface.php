<?php
/**
 * @file
 * Contains \Mpx\ClientInterface
 */

namespace Mpx;

use GuzzleHttp\Message\Response;

interface ClientInterface {

  const FORMAT_JSON = 'json';
  const FORMAT_CJSON = 'cjson';
  const FORMAT_ATOM = 'atom';
  const FORMAT_RSS = 'rss';

  /**
   * Makes a request using the GET method.
   *
   * @param string $path
   *   The path to request.
   * @param array $options
   *   Additional options for the request.
   *
   * @return Response
   *   The response for the request.
   */
  public function get($path, array $options = array());

  /**
   * Parses a request based on the requested format.
   *
   * @param Response $response
   *   The response.
   * @param string $format
   *   The format sent as a query string.
   *
   * @return array
   *   The parsed response body.
   */
  public function parseBody(Response $response, $format = '');

  /**
   * Gets the default configuration.
   *
   * @return array
   *   The defaults array.
   */
  public function getDefaults();

}
