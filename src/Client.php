<?php
/**
 * @file
 * Contains \Mpx\Client
 */

namespace Mpx;

use GuzzleHttp\Client as GuzzleClient;

class Client implements ClientInterface {

  /**
   * Guzzle client.
   *
   * @var GuzzleClient
   */
  protected $client;

}
